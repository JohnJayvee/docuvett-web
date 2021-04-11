<template>
    <el-container class="force-update-rex-model">
        <el-col v-loading="loadingForm" :offset="1">
            <el-form ref="form" :model="form" :rules="rules">
                <el-col>
                    <el-form-item label="Select model" label-width="120px">
                        <el-select v-model="form.type_id" placeholder="Select model" @change="changeModel">
                            <el-option
                                v-for="(label, id) in models"
                                :key="id"
                                :label="label"
                                :value="id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col>
                    <el-form-item label="Select account" label-width="120px">
                        <el-select v-model="form.account_id" placeholder="Select account" :disabled="!form.type_id" @change="selectAccount">
                            <el-option
                                v-for="account in accounts"
                                :key="account.account_id"
                                :label="account.name"
                                :value="account.account_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col v-if="availableAccountForUpdating">
                    <el-col>
                        <el-form-item>
                            <el-switch
                                v-model="form.is_update_model_office"
                                style="display: block"
                                active-color="#a3a2a2"
                                inactive-color="#a3a2a2"
                                active-text="Update office"
                                inactive-text="Update agent"
                                active-value="1"
                                inactive-value="0">
                            </el-switch>
                        </el-form-item>
                    </el-col>
                    <el-col>
                        <el-form-item label="Model id" label-width="120px">
                            <el-input v-model="form.model_id" @change="changeId"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col>
                        <el-button
                            :disabled="availableForUpdatingRecord"
                            @click="updateRecord">
                            Update model
                        </el-button>
                    </el-col>
                </el-col>
            </el-form>
        </el-col>
    </el-container>
</template>

<script>
    import {getByOptions} from '~/includes/endpoints';
    import {
        checkActiveParser,
        checkModelId,
        updateRexModel,
    } from '~/includes/endpoints';

    export default {
        components: {
        },
        data() {
            return {
                loadingForm: false,
                form: {
                    type_id: null,
                    account_id: null,
                    is_update_model_office: true,
                    model_id: null,
                },
                rules: {},
                accounts: {},
                models: Laravel.RexModel.ALLOW_UPDATE_MODELS,
                availableAccountForUpdating: false,
                availableForUpdatingRecord: true,
            };
        },
        mounted() {
            this.getAccounts();
        },
        methods: {
            selectAccount() {
                this.loadingForm = this.availableForUpdatingRecord = true;
                this.availableAccountForUpdating = false;
                this.form.model_id = null;
                checkActiveParser({account_id: this.form.account_id}).then(response => {
                    let params = {
                        type: 'error',
                        message: 'Account not available for update.',
                    };
                    this.availableAccountForUpdating = !response.data.active;
                    if (this.availableAccountForUpdating) {
                        params.type = 'success';
                        params.message = 'Account available for update.';
                    }
                    this.$message(params);
                }).finally(() => {
                    this.loadingForm = false;
                });
            },
            getAccounts() {
                getByOptions({
                    'options': [
                        'rex_accounts',
                    ]
                }).then(response => {
                    this.accounts = JSON.parse(response.data[0]['value']);
                });
            },
            changeId(id) {
                this.loadingForm = this.availableForUpdatingRecord = true;
                checkModelId({
                    'model_id': id,
                    'type': this.form.type_id
                }).then((res) => {
                    this.availableForUpdatingRecord = !res.data.available;
                    this.$message(res.data);
                }).finally(() => {
                    this.loadingForm = false;
                });
            },
            updateRecord() {
                this.loadingForm = true;
                updateRexModel(this.form).then((res) => {
                    this.$message(res.data);
                }).finally(() => {
                    this.loadingForm = false;
                });
            },
            changeModel() {
                this.availableAccountForUpdating = false;
                this.availableForUpdatingRecord = true;
                this.form.account_id = null;
            }
        }
    };
</script>
<style lang="scss" scoped>

</style>