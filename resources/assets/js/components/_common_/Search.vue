<template>
    <div v-if="moduleAvailable" class="global-search">
        <el-autocomplete
            v-model="searchValue"
            class="inline-input"
            :fetch-suggestions="search"
            placeholder="Search..."
            prefix-icon="el-icon-search"
            :trigger-on-focus="false">
            <template slot-scope="{ item }">
                <div class="search-results">
                    <div v-if="item.empty" class="no-results">
                        No results found
                    </div>
                    <div v-if="item.error" class="no-results">
                        Search unavailable
                    </div>
                    <div v-if="item.invalid" class="no-results">
                        Not allowed chars
                        <span class="chars"> + - =  > < ! ( ) { } [ ] ^ " ~ * ? : \ / </span>
                    </div>
                    <div v-if="!item.empty && !item.invalid && !item.error" @click="searchInit(item._source.model_id, item._source.route)">
                        <div class="result">
                            <span class="field">{{ item._source.searchField }}:</span>
                            <span class="value" v-html="item._source.value"></span>
                        </div>
                        <span class="type">{{ item._source.model_name }}</span>
                    </div>
                </div>
            </template>
        </el-autocomplete>
    </div>
</template>

<script>
    import {methods} from '../../includes/_common_/util';
    import {
        globalSearch,
        getNote
    } from '../../includes/endpoints';
    import axios from 'axios';

    const CancelToken = axios.CancelToken;

    export default {
        data() {
            return {
                moduleName: window.Laravel.modules._common_.Search.name,
                searchValue: '',
                searchSource: CancelToken.source(),
            };
        },
        methods: {
            ...methods,
            search(queryString, cb) {
                // let pattern = /[\*\+\-=~><\"\?^\${}\(\)\:\!\/[\]\\\s]/g;
                // let inValid = pattern.test(queryString);
                //
                // if (inValid) {
                //     cb([{invalid: true}]);
                //
                //     return;
                // }

                if (this.searchSource) {
                    this.searchSource.cancel('Fetch another search results');
                }
                this.searchSource = CancelToken.source();
                let _this = this;

                globalSearch(
                    {search: queryString},
                    {cancelToken: this.searchSource.token}
                ).then((response) => {
                    let results = response.data;

                    if (results.error) {
                        cb([results]);

                        return;
                    }

                    results.forEach(function (item) {
                        item._source.searchField = item._source.searchField
                            .replace(/([A-Z])/g, ' $1')
                            .replace(/_/g, ' ');
                        item._source.value = item._source.value
                            .replace(new RegExp('(' + (queryString.split(' ').join('|') + ')').replace('+', '\\+'), 'gi'), '<span class="highlight">$1</span>');
                    });
                    if (results.length === 0) {
                        results[results.length] = {empty: true};
                    }
                    cb(results);
                }).catch(function(error) {
                    /* Show error only where is the no just a cancelled request */
                    if (!error.__CANCEL__) {
                        if (Laravel.appDebug) {
                            console.log(error);
                        }
                        if (error.message || (error.response && error.response.data.message)) {
                            _this.$message.error(error.message ? error.message : error.response.data.message);
                        } else {
                            _this.$message.error('Unknown server error');
                        }
                    }
                });
            },
            searchInit(id, route) {
                // if search for Note - redirect to Contact/Company and show Note
                if (route === window.Laravel.Note.SEARCH_PARAM_ROUTE) {
                    return this.searchNoteRedirect(id, route);
                }

                // if search for Contact, Company, User, Portfolio
                switch(route) {
                    case Laravel.User.SEARCH_PARAM_ROUTE:
                        this.$router.push({name: 'User', params: {id: id}});
                        break;
                    case Laravel.Contact.SEARCH_PARAM_ROUTE:
                        this.$router.push({name: this.getSingularForm('Contacts'), params: {id: id}});
                        break;
                    case Laravel.Company.SEARCH_PARAM_ROUTE:
                        this.$router.push({name: this.getModuleTitle('Companies') + ' Details', params: {id: id}});
                        break;
                    case Laravel.Portfolio.SEARCH_PARAM_ROUTE:
                        this.$router.push({name: 'Portfolio', params: {id: id}});
                        break;
                    default:
                        if (this.$router.currentRoute.name === route) {
                            Vue.$emit('searchInit' + route, id);
                        } else {
                            this.$router.push({name: route, params: {searchId: id}});
                        }
                }

            },
            searchNoteRedirect(id, route) {
                // query note data
                getNote({id: id}).then((response) => {
                    const note = response.data;

                    // check if Note has Contact/Company attached and redirect to
                    if (note.contact.length) {

                        if (
                            this.$router.currentRoute.name === 'Contact' &&
                            this.$router.currentRoute.params.id == note.contact[0].id
                        ) {
                            // user is already on Contact's page
                            bus.$emit('viewNote', {
                                id: note.id
                            });
                        } else {
                            // redirect
                            this.$router.push({name: 'Contact', params: {id: note.contact[0].id}, query: {note_id: id}});
                        }

                    } else if (note.company.length) {

                        if (
                            this.$router.currentRoute.name === (this.getModuleTitle('Companies') + ' Details') &&
                            this.$router.currentRoute.params.id == note.company[0].id
                        ) {
                            // user is already on Company's page
                            bus.$emit('viewNote', {
                                id: note.id
                            });
                        } else {
                            // redirect
                            this.$router.push({name: this.getModuleTitle('Companies') + ' Details', params: {id: note.company[0].id}, query: {note_id: id}});
                        }

                    } else {
                        this.$message.error('Note can\'t be shown: no Contact or Company attached.');
                    }
                });
            },
        },
        computed: {
            moduleAvailable() {
                return this.$store.state.Modules.availableList[this.moduleName];
            },
        },
    };
</script>