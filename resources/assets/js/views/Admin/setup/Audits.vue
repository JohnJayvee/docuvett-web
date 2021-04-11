<template>
    <section id="audits">
        <h2 class="page-title">
            Audits
        </h2>
        <breadcrumb></breadcrumb>
        <!-- filters -->
        <el-col :span="24" class="m-t-10" style="padding-bottom: 0px;">
            <el-form :inline="true" :model="filters" size="mini" @submit.native.prevent="fetchAudits">
                <el-form-item class="search-input">
                    <el-input v-model="filters.search" placeholder="Search..." @input="applySearch">
                        <i v-if="filters.search.length" slot="suffix" class="el-input__icon el-icon-error" @click="clearSearch"></i>
                    </el-input>
                </el-form-item>
            </el-form>
        </el-col>

        <!-- table -->
        <el-table
            v-loading="listLoading"
            :data="audits"
            highlight-current-row
            style="width: 100%;"
            @sort-change="handleSortChange"
            @filter-change="handleFilterChange">
            <el-table-column prop="auditable_type" label="Type" min-width="120" sortable="false"></el-table-column>
            <el-table-column prop="auditable_id" label="ID" width="70" sortable="false"></el-table-column>
            <el-table-column prop="event" label="Event" width="90" sortable="false"></el-table-column>
            <el-table-column prop="user_id" label="By" width="150" sortable="false">
                <template slot-scope="scope">
                    <span v-if="scope.row.user">{{ scope.row.user.name }}</span>
                    <span v-else class="grey-text">*unathorized*</span>
                </template>
            </el-table-column>
            <el-table-column v-if="isTagsAvailable" prop="tags" label="Tags" width="150" sortable="false"></el-table-column>
            <el-table-column prop="created_at" label="Created" width="165" sortable="false">
                <template slot-scope="created_at">
                    {{ GlobalFormatDate(created_at.row.created_at) }}
                </template>
            </el-table-column>
            <el-table-column label="Action" width="80" align="right">
                <template slot-scope="scope">
                    <el-tooltip
                        :disabled="formVisible"
                        content="Show"
                        placement="top"
                        :open-delay="300">
                        <el-button size="mini" icon="el-icon-fa-eye" @click="handleView(scope.$index, scope.row)"></el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>

        <!-- pagination -->
        <el-col :span="24" class="el-pagination">
            <el-pagination
                layout="sizes, prev, pager, next"
                :current-page.sync="page"
                :page-size.sync="pageSize"
                :total="total"
                class="pull-right">
            </el-pagination>
        </el-col>

        <!-- form dialog -->
        <el-dialog title="Audit Details" :visible.sync="formVisible" :append-to-body="true" class="audits-dialog" width="80%">
            <el-form label-width="100px">
                <el-form-item label="Type">
                    <span>{{ form.auditable_type }}</span>
                </el-form-item>
                <el-form-item label="ID">
                    <span>{{ form.auditable_id }}</span>
                </el-form-item>
                <el-form-item label="By">
                    <span v-if="form.user">{{ form.user.name }}</span>
                    <i v-else class="grey-text">*unathorized*</i>
                </el-form-item>
                <el-form-item v-if="isTagsAvailable" label="Tags">
                    <span>{{ form.tags }}</span>
                </el-form-item>
                <el-form-item label="Created">
                    <span>{{ GlobalFormatDate(form.created_at) }}</span>
                </el-form-item>
                <el-form-item label="Event">
                    <span>{{ form.event }}</span>
                </el-form-item>
                <el-form-item label="IP Address">
                    <span>{{ form.ip_address }}</span>
                </el-form-item>
                <el-form-item label="URL">
                    <span>{{ form.url }}</span>
                </el-form-item>
                <el-form-item label="User Agent">
                    <span>{{ form.user_agent }}</span>
                </el-form-item>
                <el-form-item label="Changes">
                    <table v-if="(Object.keys(form.old_values).length + Object.keys(form.new_values).length) !== 0" class="changes-table">
                        <tbody>
                            <tr v-for="(prop, key) in form.old_values" :key="key">
                                <td align="right">
                                    <b class="p-r-10">{{ key }}:</b>
                                </td>
                                <td>
                                    <span v-if="!emptyValues.includes(prop)">{{ prop }}</span>
                                    <i v-else class="grey-text">*empty*</i>
                                </td>
                                <td width="40" class="text-center">
                                    <i class="el-icon-d-arrow-right"></i>
                                </td>
                                <td>
                                    <span v-if="!emptyValues.includes(form.new_values[key])">{{ form.new_values[key] }}</span>
                                    <i v-else class="grey-text">*empty*</i>
                                </td>
                            </tr>
                            <tr v-for="(prop, j) in form.new_values" :key="prop">
                                <template v-if="!form.old_values.hasOwnProperty(j)">
                                    <td align="right">
                                        <b class="p-r-10">{{ j }}:</b>
                                    </td>
                                    <td>
                                        <span v-if="!emptyValues.includes(form.old_values[j])">{{ form.old_values[j] }}</span>
                                        <i v-else class="grey-text">*empty*</i>
                                    </td>
                                    <td width="40" class="text-center">
                                        <i class="el-icon-d-arrow-right"></i>
                                    </td>
                                    <td>
                                        <span v-if="!emptyValues.includes(prop)">{{ prop }}</span>
                                        <i v-else class="grey-text">*empty*</i>
                                    </td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                    <i v-else class="grey-text">*no visible changes*</i>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click.native="formVisible = false">
                    Close
                </el-button>
            </div>
        </el-dialog>
    </section>
</template>

<style lang="scss">
    @import "../../../../sass/element-ui-colors";
    @import "../../../../sass/element-ui-variables";
    #audits{
        .search-input{
            float: right;
        }
        .suspended {
            color: $--color-danger;
        }
    }
    .audits-dialog{
        .el-dialog__body{
            padding-top: 10px;
        }
        .changes-table {
            border-collapse: collapse;
            width: 100%;
            td {
                vertical-align: top;
                line-height: 22px;
            }
            tr:not(:last-child) {
                td {
                    border-bottom: $--border-base;
                }
            }
        }
        .el-form-item__label, .el-form-item__content {
            line-height: 22px;
        }
    }
</style>

<script>
    import {methods, filters, computed} from '../../../includes/_common_/util';
    import Errors               from '../../../includes/_common_/Errors';
    import axios                from 'axios';
    import Breadcrumb           from "../Breadcrumb";

    import {getAuditList}       from '../../../includes/endpoints';

    const CancelToken = axios.CancelToken;

    export default {
        filters,
        data() {
            return {
                sortBy: 'id,desc',
                filters: {
                    search: ''
                },
                audits: [],
                errors: new Errors(),
                total: 0,
                page: 1,
                pageSize: 10,
                listLoading: true,
                listSource: CancelToken.source(),
                formVisible: false,
                formLoading: false,
                form: {
                    id: '',
                    old_values: {},
                    new_values: {},
                    user: null,
                },
                rules: {},
                emptyValues: [
                    null,
                    undefined,
                    ''
                ],
            };
        },
        methods: {
            ...methods,
            handleSortChange(val) {
                if (val.prop != null) {
                    var sort = val.order.startsWith('a') ? 'asc' : 'desc';
                    this.sortBy = val.prop + ',' + sort;
                    this.fetchAudits();
                }
            },
            handleFilterChange(val) {
                this.fetchAudits();
            },
            fetchAudits() {
                if (this.listSource) {
                    this.listSource.cancel('Fetch another audits list');
                }
                this.listSource = CancelToken.source();
                let params = {
                    page: this.page,
                    search: this.filters.search,
                    sortBy: this.sortBy,
                    pageSize: this.pageSize
                };
                this.listLoading = true;
                getAuditList(
                    params,
                    {
                        cancelToken: this.listSource.token
                    }
                ).then((res) => {
                    this.total = res.data.total;
                    this.audits = res.data.data;
                    this.listLoading = false;
                    this.listSource = false;
                });
            },
            handleView(index, row) {
                this.form = Object.assign({}, row);
                this.formVisible = true;
            },
            applySearch: _.debounce(function (e) {
                this.fetchAudits();
            }, 300),
            clearSearch() {
                this.filters.search = '';
                this.fetchAudits();
            }
        },
        components: {
            Breadcrumb,
        },
        computed: {
            ...computed,
            _: _,
        },
        watch: {
            page: function (val) {
                this.fetchAudits();
            },
            pageSize: function (val) {
                this.fetchAudits();
            },
        },
        mounted() {
            this.fetchAudits();
        }
    };
</script>
