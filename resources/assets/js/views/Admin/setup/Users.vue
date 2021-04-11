<template>
    <section id="users">
        <h2 class="page-title">
            Users
        </h2>
        <!-- filters -->
        <el-col :span="24" class="m-t-10" style="padding-bottom: 0px;">
            <el-form :inline="true" :model="filters" size="mini" @submit.native.prevent="fetchUsers">
                <el-form-item>
                    <el-button type="primary" icon="plus" @click="handleAdd">
                        Add
                    </el-button>
                </el-form-item>
                <el-form-item class="search-input">
                    <el-input v-model="filters.search" placeholder="Search..." @input="applySearch">
                        <i
                            v-if="filters.search.length"
                            slot="suffix"
                            class="el-input__icon el-icon-error"
                            @click="clearSearch"></i>
                    </el-input>
                </el-form-item>
            </el-form>
        </el-col>

        <!-- table -->
        <el-table
            v-loading="listLoading"
            :data="users"
            highlight-current-row
            style="width: 100%;"
            @sort-change="handleSortChange"
            @filter-change="handleFilterChange">
            <el-table-column prop="name" label="Full Name" min-width="230" sortable="false">
                <template slot-scope="scope">
                    <img v-if="scope.row.avatar" :src="scope.row.avatar" class="pull-left m-r-5 user-avatar-image">
                    <i v-else class="m-r-5 el-icon-picture-outline pull-left no-user-avatar-icon"></i>
                    <span style="line-height: 40px;">{{ scope.row.name }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="email" label="Email" min-width="230" sortable="false"></el-table-column>
            <el-table-column prop="phone" label="Phone" width="150" sortable="false"></el-table-column>
            <el-table-column prop="suspended" label="Status" width="105" sortable="false">
                <template slot-scope="scope">
                    <span :class="{suspended: !!scope.row.suspended}">
                        {{ scope.row.suspended ? 'Suspended' : 'Active' }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column label="Actions" width="130">
                <template slot-scope="scope">
                    <el-button size="mini" icon="el-icon-edit" @click="handleEdit(scope.row)"></el-button>
                    <el-button
                        v-if="scope.row.id != $auth.user().id && scope.row.maxUserLevel <= $auth.user().maxUserLevel"
                        type="danger"
                        size="mini"
                        icon="el-icon-delete"
                        @click="handleDelete(scope.row)">
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- pagination -->
        <el-col :span="24" class="el-pagination">
            <el-pagination
                layout="sizes, prev, pager, next"
                :page-size="pageSize"
                :total="total"
                style="float:right;"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange">
            </el-pagination>
        </el-col>

        <!-- form dialog -->
        <el-dialog
            :title="formTitle"
            :visible.sync="formVisible"
            :close-on-click-modal="false"
            :append-to-body="true"
            class="users-dialog">
            <el-form
                ref="form"
                :model="form"
                label-width="140px"
                :rules="rules"
                size="small"
                @submit.native.prevent="saveSubmit">
                <el-row :gutter="20" style="display: flex;">
                    <el-col :span="16">
                        <el-form-item label="Name" prop="name" :error="errors.get('name')">
                            <el-input
                                v-model="form.name"
                                placeholder="Full name"
                                suffix-icon="el-icon-edit"
                                @change="errors.clear('name')">
                            </el-input>
                        </el-form-item>
                        <el-form-item
                            :label="form.id ? 'New Password' : 'Password'"
                            prop="password"
                            :error="errors.get('password')"
                            :required="!form.id">
                            <el-row style="margin-bottom: 18px;">
                                <el-input
                                    v-model="form.password"
                                    type="password"
                                    suffix-icon="el-icon-fa-key"
                                    placeholder="Enter password"
                                    @change="errors.clear('password')">
                                </el-input>
                            </el-row>
                            <el-row>
                                <el-input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    suffix-icon="el-icon-fa-key"
                                    placeholder="Confirm password">
                                </el-input>
                            </el-row>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" class="el-form-item el-form-item--small user-avatar-container">
                        <el-upload
                            class="user-avatar-placeholder"
                            action=""
                            :show-file-list="false"
                            :auto-upload="false"
                            accept="image/*"
                            :on-change="beforeAvatarUpload">
                            <div v-if="form.avatar" class="user-avatar-preview">
                                <img :src="form.avatar">
                                <el-button
                                    type="danger"
                                    size="mini"
                                    icon="el-icon-delete"
                                    @click.stop="clearCompanyAvatar">
                                </el-button>
                            </div>
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                    </el-col>
                </el-row>
                <el-form-item label="Email" prop="email" :error="errors.get('email')">
                    <el-input
                        v-model="form.email"
                        suffix-icon="el-icon-fa-envelope-o"
                        placeholder="Valid email address"
                        @change="errors.clear('email')">
                    </el-input>
                </el-form-item>
                <el-form-item label="Phone" prop="phone" :error="errors.get('phone')">
                    <el-input
                        v-model="form.phone"
                        type="phone"
                        placeholder="Mobile phone number"
                        auto-complete="on"
                        suffix-icon="el-icon-fa-phone"
                        @change="errors.clear('phone')"
                        @input="handlePhoneInput"></el-input>
                </el-form-item>
                <el-form-item label="User role" prop="role" :error="errors.get('role')">
                    <el-select
                        v-model="form.role"
                        placeholder="- Choose User Role -"
                        :disabled="form.id && !$auth.check('roles.update')"
                        style="width: 100%;"
                        @change="errors.clear('role')">
                        <el-option
                            v-for="role in roles"
                            :key="role.id"
                            :label="role.display_name"
                            :value="role.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item
                    label="Suspend Account"
                    class="clear-none w-140"
                    prop="suspended"
                    :error="errors.get('suspended')">
                    <el-switch
                        v-model="form.suspended"
                        :active-value="1"
                        :inactive-value="0">
                    </el-switch>
                </el-form-item>
                <el-form-item label="Tags" prop="tagList" :error="errors.get('tagList')">
                    <el-select
                        v-model="form.tagList"
                        multiple
                        filterable
                        allow-create
                        remote
                        :remote-method="fetchTags"
                        :loading="tagsLoading"
                        default-first-option
                        style="width: 100%"
                        placeholder="">
                        <el-option
                            v-for="tag in tags"
                            :key="tag.id"
                            :label="tag.displayName"
                            :value="tag.displayName">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click.native="formVisible = false">
                    Cancel
                </el-button>
                <el-button type="primary" :loading="formLoading" @click.native="saveSubmit">
                    Save
                </el-button>
            </div>
        </el-dialog>
    </section>
</template>

<style lang="scss">
    @import "../../../../sass/element-ui-colors";
    @import "../../../../sass/element-ui-variables";
    @import "../../../../sass/element-ui-defaults";

    #users {
        .user-avatar-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 20px;
        }

        .no-user-avatar-icon {
            width: 40px;
            line-height: 40px;
            font-size: 25px;
            color: $--color-text-placeholder;
            text-align: center;
        }

        .search-input {
            float: right;
        }

        .suspended {
            color: $--color-danger;
        }
    }

    .users-dialog {
        .el-dialog__body {
            padding-top: 10px;
        }

        .user-avatar-container {
            display: flex;

            .user-avatar-placeholder {
                width: 100%;
                cursor: pointer;
                border-style: dashed;
                border-width: 1px;
                border-color: $--border-color-base;
                border-radius: $--border-radius-base;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: 0.3s;

                .el-upload {
                    width: 100%;
                    display: block;

                    .el-icon-plus {
                        transition: 0.3s;
                        font-size: 30px;
                        line-height: 130px;
                        color: $--border-color-base;
                    }

                    .user-avatar-preview {
                        position: relative;
                        display: flex;

                        img {
                            width: 100%;
                            height: 130px;
                            object-fit: contain;
                            border-radius: $--border-radius-base;
                        }

                        .el-button--danger {
                            position: absolute;
                            right: 10px;
                            bottom: 10px;
                        }

                        &:before {
                            transition: 0.3s;
                            opacity: 0;
                            pointer-events: none;
                            content: ' ';
                            background-color: black;
                            position: absolute;
                            top: 0;
                            width: 100%;
                            bottom: 0;
                            border-radius: $--border-radius-base;
                        }

                        &:hover:before {
                            opacity: 0.4;
                        }
                    }
                }

                &:hover {
                    border-color: $--color-primary;

                    .el-icon-plus {
                        color: $--color-primary;
                    }
                }
            }
        }

        .el-date-editor.el-input {
            width: calc(50% - 15px);
            float: left;

            input {
                padding-right: 0;
            }
        }

        .w-140 {
            .el-form-item__label {
                width: 140px !important;
                @media screen and (max-width: $--xs - 1) {
                    text-align: left !important;
                }
            }
        }
    }
</style>

<script>
    import {methods} from '../../../includes/_common_/util';
    import Errors from '../../../includes/_common_/Errors';
    import app_data from '../../../includes/app_data';
    import VueGoogleAutocomplete from 'vue-google-autocomplete';
    import axios from 'axios';

    import {
        getUserList,
        getUser,
        addUser,
        editUser,
        removeUser,
        importUser,
        exportUser,
        getRoleList,
        currentUser,
        downloadMessagesHistory,
        getTagAutocomplete,
        getUserAutocomplete
    } from '../../../includes/endpoints';

    const CancelToken = axios.CancelToken;

    export default {
        components: {
        },
        data() {
            return {
                appEnable: Laravel.appEnable,
                sortBy: 'id,asc',
                filters: {
                    search: ''
                },
                users: [],
                roles: [],
                tags: [],
                errors: new Errors(),
                total: 0,
                page: 1,
                pageSize: 10,
                listLoading: true,
                listSource: CancelToken.source(),
                formVisible: false,
                formLoading: false,
                formTitle: 'New User',
                tagsLoading: false,
                form: {
                    tagList: [],
                    suspended: 0,
                },
                rules: {
                    name: [{required: true}],
                    email: [{required: true}, {type: 'email', message: 'please use a valid email address'}],
                    role: [{required: true}],
                    phone: [{required: true}, {validator: this.validatePhoneNumber}],
                },
                countries: app_data.countryList,
                importFormVisible: false,
                importFormLoading: false,
                exportLoading: false,

            };
        },
        computed: {
            isCustomer: function () {
                let role = _.find(this.roles, {name: 'customer'});
                return role ? (role.id == this.form.role) : false;
            },
        },
        methods: {
            ...methods,
            handleSortChange(val) {
                if (val.prop != null) {
                    var sort = val.order.startsWith('a') ? 'asc' : 'desc';
                    this.sortBy = val.prop + ',' + sort;
                    this.fetchUsers();
                }
            },
            handleFilterChange(val) {
                this.fetchUsers();
            },
            handleCurrentChange(val) {
                this.page = val;
                this.fetchUsers();
            },
            handleSizeChange(val) {
                this.pageSize = val;
                this.fetchUsers();
            },
            fetchUsers() {
                if (this.listSource) {
                    this.listSource.cancel('Fetch another users list');
                }
                this.listSource = CancelToken.source();
                let params = {
                    page: this.page,
                    search: this.filters.search,
                    sortBy: this.sortBy,
                    pageSize: this.pageSize
                };
                this.listLoading = true;
                getUserList(
                    params,
                    {
                        cancelToken: this.listSource.token
                    }
                ).then((res) => {
                    this.total = res.data.total;
                    this.users = res.data.data;
                    this.listLoading = false;
                    this.listSource = false;
                });
            },
            fetchRoles() {
                getRoleList({}).then((res) => {
                    this.roles = res.data.data;
                });
            },
            fetchTags(query) {
                if (query !== '') {
                    this.tagsLoading = true;
                    getTagAutocomplete({search: query}).then((res) => {
                        this.tags = res.data;
                        this.tagsLoading = false;
                    });
                } else {
                    this.tags = [];
                }
            },
            countrySearch(query, cb) {
                query = query ? query.toLowerCase() : '';
                let countries = this.countries;
                let results = query ? countries.filter((country) =>
                    country.name.toLowerCase().includes(query)
                ) : countries;
                // call callback function to return suggestions
                cb(results);
            },
            handleDelete(row) {
                this.$confirm('This will permanently delete the user. Continue?', 'Warning', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    removeUser(row.id).then((response) => {
                        this.listLoading = false;
                        this.$message.success(response.data.message);
                        this.fetchUsers();
                    }).catch((error) => {
                        this.listLoading = false;
                        if (error.response.data.message) {
                            this.$message.error(error.response.data.message);
                        } else {
                            this.$message.error('Unknown server error');
                        }
                    });
                });
            },
            handleEdit(row) {
                this.formTitle = 'Edit User';
                this.form = Object.assign({}, row);
                this.errors = new Errors();
                this.formVisible = true;
                if (this.$refs['form']) {
                    this.$refs['form'].resetFields();
                }
            },
            handleAdd() {
                this.formTitle = 'New User';
                this.form = {
                    tagList: [],
                    suspended: 0,
                };
                this.errors = new Errors();
                this.formVisible = true;
                if (this.$refs['form']) {
                    this.$refs['form'].resetFields();
                }
            },
            handleTagsChange(newTags) {
                this.errors.clear('tagList');
                this.form.tagList = newTags;
            },
            handleAddressChange(data) {
                this.errors.clear('address');
                if (!data.newVal) {
                    this.$set(this.form, 'country', '');
                    this.$set(this.form, 'city', '');
                    this.$set(this.form, 'postcode', '');
                    this.$set(this.form, 'address', '');
                }
                this.$nextTick(() => {
                    this.$refs['form'].validateField('address', () => {
                    });
                });
            },
            getAddressData(addressData, placeResultData, id) {
                this.$set(this.form, 'country', addressData.country);
                this.$set(this.form, 'city', addressData.locality);
                this.$set(this.form, 'postcode', addressData.postal_code);
                this.$set(this.form, 'address', document.getElementById(id).value);
            },
            handlePhoneInput() {
                this.$nextTick(() => {
                    this.$set(this.form, 'phone', this.formatPhoneNumber(this.form.phone, false));
                });

            },
            saveSubmit() {
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        this.formLoading = true;
                        this.errors.clear();
                        let action = this.form.id ? editUser : addUser;

                        action(this.prepareUserData()).then((response) => {
                            this.formLoading = false;
                            this.$message.success(response.data.message);
                            this.formVisible = false;
                            this.fetchUsers();
                            if (this.form.id === this.$auth.user().id) {
                                this.$auth.fetch();
                            }
                        }).catch((error) => {
                            if (Laravel.appDebug) {
                                console.log(error);
                            }
                            if (error.response.data.errors) {
                                this.errors.record(error.response.data.errors);

                            } else if (error.response.data.message) {
                                this.$message.error(error.response.data.message);
                            } else {
                                this.$message.error('Unknown server error');
                            }
                            this.formLoading = false;
                        });
                    }
                });
            },
            prepareUserData() {
                let formData = new FormData();
                for (let field in this.form) {
                    formData.append(field, this.form[field]);
                }
                for (let tag in this.form.tagList) {
                    formData.append('tags[]', this.form.tagList[tag]);
                }
                formData.set('_method', this.form.id ? 'PUT' : 'POST');
                if (this.form.file) {
                    formData.set('file', this.form.file);
                }
                return formData;
            },
            applySearch: _.debounce(function (e) {
                this.fetchUsers();
            }, 300),
            clearSearch() {
                this.filters.search = '';
                this.fetchUsers();
            },
            beforeAvatarUpload(file) {
                if (file.size / 1024 / 1024 > 2) {
                    this.$message.error('Picture size can not exceed 2MB!');
                    return false;
                }
                this.form.avatar = URL.createObjectURL(file.raw);
                this.form.file = file.raw;
                return true;
            },
            clearCompanyAvatar() {
                this.form.avatar = '';
                this.form.file = '';
            },
            submitUpload() {
                this.$refs.upload.submit();
            },
            toggleImportForm() {
                if (this.$refs.upload) {
                    this.$refs.upload.clearFiles();
                }
                this.importFormVisible = !this.importFormVisible;
            }

        },
        mounted() {
            this.fetchRoles();
            this.fetchUsers();
            this.listenSearchEvent(getUser, 'Users');
        }
    };
</script>