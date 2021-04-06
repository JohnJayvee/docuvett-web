<template>
    <section id="settings">
        <h2 class="page-title">
            System Settings
        </h2>
        <el-card v-loading="loading">
            <el-form v-if="Object.keys(fields).length" label-width="250px" @submit.native.prevent="handleSave">
                <el-tabs v-model="activeTab">
                    <el-tab-pane v-for="(section, key) in fields" :key="key" :name="key">
                        <div slot="label">
                            <span class="tab-title">{{ section.title }}</span>
                        </div>
                        <p>{{ section.description }}</p>
                        <br>
                        <el-form-item
                            v-for="(field) in section.elements"
                            :key="field.name"
                            :label="field.label"
                            :error="errors.get(field.name)">
                            <el-switch v-if="field.type === 'switch'" v-model="form[field.name]" :active-value="'1'" :inactive-value="'0'"></el-switch>
                            <el-select v-else-if="field.type === 'select'" v-model="form[field.name]" :multiple="field.multiple" placeholder="Select">
                                <el-option
                                    v-for="item in getOptions(field)"
                                    :key="item.account_id"
                                    :label="item.name"
                                    :value="item.account_id">
                                </el-option>
                            </el-select>
                            <el-input v-else v-model="form[field.name]" :type="field.type" autocomplete="off"></el-input>
                        </el-form-item>
                    </el-tab-pane>
                </el-tabs>
                <div class="text-right">
                    <el-button v-if="changed" :disabled="saveLoading" @click="handleCancel">
                        Cancel
                    </el-button>
                    <el-button :disabled="!changed" type="primary" :loading="saveLoading" @click="handleSave">
                        Save
                    </el-button>
                </div>
            </el-form>
            <el-alert
                v-else
                class="m-t-20"
                title="Ooops! Something wrong..."
                type="error"
                :closable="false"
                show-icon
                description="Setting's fields are empty. Please, check the config file.">
            </el-alert>
        </el-card>
    </section>
</template>

<style lang="scss">
@import "../../../../sass/element-ui-colors";
@import "../../../../sass/element-ui-variables";
@import "../../../../sass/element-ui-defaults";

#settings {
    .card-header {
        line-height: $--input-small-height;
    }

    .tab-title {
        padding: 0 15px;
    }

    .el-select {
        width: 100%;
    }

    @media screen and (max-width: $--sm - 1) {
        .el-form-item__label {
            text-align: left;
            line-height: 24px;
        }
        .el-form-item__content {
            clear: both;
            margin-left: 0 !important;
        }
    }
}
</style>

<script>
    import {methods, filters, computed} from '../../../includes/_common_/util';
    import Errors from '../../../includes/_common_/Errors';

    import {getSettings, saveSettings} from '../../../includes/endpoints';

    export default {
        data() {
            return {
                loading: false,
                saveLoading: false,
                activeTab: 'general',
                settings: Laravel.appSettings,
                fields: Laravel.settings_fields,
                form: {},
                errors: new Errors(),
                initialForm: {},
            };
        },
        methods: {
            getOptions(data) {
                let item = _.find(this.settings, {name: data.name});
                let result = [];
                if (item && item['options']) {
                    return item['options'];
                }

                return result;
            },
            handleSave() {
                if (this.changed) {
                    this.saveLoading = true;
                    saveSettings(this.prepareForm()).then(response => {
                        this.$message.success('Successfully saved');
                        this.initialForm = _.cloneDeep(this.form);
                        Laravel.appSettings = _.cloneDeep(response.data);
                        this.saveLoading = false;
                        this.errors.clear();
                    }).catch(error => {
                        if (Laravel.appDebug) {
                            console.log(error);
                        }

                        if (error.response.data.errors) {
                            this.errors.record(error.response.data.errors);
                        }

                        if (error.response.data.message) {
                            this.$message.error(error.response.data.message);
                        } else {
                            this.$message.error('Unknown server error');
                        }
                        this.saveLoading = false;
                    });
                }
                return false;
            },
            handleCancel() {
                this.form = _.cloneDeep(this.initialForm);
            },
            prepareForm() {
                let result = {};
                for (let key in this.form) {
                    switch (key) {
                        case 'rex_accessible_accounts':
                        case 'rex_forced_update':
                        case 'full_sync_office':
                        case 'full_sync_agent':
                            if (_.isEmpty(this.form[key])) {
                                result[key] = '[]';
                            } else {
                                result[key] = this.form[key];
                            }
                            break;
                        default:
                            result[key] = this.form[key];
                            break;
                    }
                }

                return result;
            }
        },
        computed: {
            ...computed,
            changed: function () {
                return !_.isEqual(this.form, this.initialForm);
            }
        },
        mounted() {
            this.initialForm = Object.assign({}, this.form);

            getSettings().then(response => {
                this.settings = response.data;

                if (Object.keys(this.fields).length) {
                    this.activeTab = Object.keys(this.fields)[0];

                    for (let section in this.fields) {
                        if (this.fields.hasOwnProperty(section)) {
                            this.fields[section].elements.forEach(field => {
                                let setting = _.find(this.settings, {name: field.name});

                                this.$set(this.form, field.name, (setting ? setting.value : field.value));
                            });
                        }
                    }
                }
            });
        }
    };
</script>