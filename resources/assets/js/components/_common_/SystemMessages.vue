<template>
    <section v-if="messages.length" class="box-card connections">
        <el-table v-loading="listLoading" :show-header="false" :data="messages" style="width: 100%" class="no-scroll-table">
            <el-table-column prop="message">
                <template slot-scope="scope">
                    <div class="secondary-color">
                        <i class="el-icon-time"></i> {{ scope.row.updated_at | shortDateTime }}
                    </div>
                    <p style="margin: 0;" class="message-text" @click="viewMessage(scope.row)" v-html="nl2br(createTextLinks(scope.row.message.trim()))"></p>
                    <br>
                </template>
            </el-table-column>
            <el-table-column v-if="$auth.check('system-messages.dismiss')" label="Action" align="right" width="40">
                <template slot-scope="scope">
                    <div class="connection-buttons">
                        <el-button
                            type="text"
                            icon="el-icon-fa-eye"
                            title="Mark as read"
                            @click="dismissMessage(scope.row)">
                        </el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </section>
</template>

<style lang="scss">
    @import "../../../sass/element-ui-colors";
    @import "../../../sass/element-ui-variables";
</style>

<script>
    import {methods, filters} from '../../includes/_common_/util';
    import util from '../../includes/_common_/util';

    import {getSystemMessageList, dismissSystemMessage} from '../../includes/endpoints';

    export default {
        filters,
        data() {
            return {
                listLoading: true,
                messages: [],
            };
        },
        methods: {
            ...methods,
            fetchMessages() {
                if (this.$auth.check('system-messages.list')) {
                    this.listLoading = true;
                    getSystemMessageList().then((res) => {
                        this.messages = res.data;
                        this.listLoading = false;
                    });
                }
            },
            dismissMessage(message) {
                this.listLoading = true;
                dismissSystemMessage(message.id).then(response => {
                    this.$message.success(response.data.message);
                    this.fetchMessages();
                }).catch(error => {
                    if (error.response.data.errors) {
                        this.tagErrors.record(error.response.data.errors);
                    } else if (error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Unknown server error');
                    }
                    this.listLoading = false;
                });
            },
            newSystemMessage(e) {
                const msgIndx = _.findIndex(this.messages, {id: e.data.id});
                if (msgIndx < 0) {
                    this.messages.unshift(e.data);
                } else {
                    this.$set(this.messages, msgIndx, e.data);
                }
            },
        },
        mounted() {
            this.fetchMessages();

            /* Subscribe to events */
            bus.$on('SystemMessageReceived', this.newSystemMessage);
        },
        beforeDestroy() {
            /* Unsubscribe from events */
            bus.$off('SystemMessageReceived', this.newSystemMessage);
        }
    };
</script>
