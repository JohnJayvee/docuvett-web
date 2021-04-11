<template>
    <section id="roles">
        <h2 class="page-title">
            Roles & Permissions
        </h2>
        <breadcrumb></breadcrumb>
        <!-- filters -->
        <el-col :span="24" class="m-t-10 p-b-0">
            <el-form :inline="true" :model="filters" size="mini" @submit.native.prevent="fetchRoles">
                <el-form-item>
                    <el-button
                        :disabled="!$auth.check('roles.create')"
                        type="primary"
                        icon="el-icon-plus"
                        @click="handleAdd">
                        Add
                    </el-button>
                </el-form-item>
                <el-form-item class="search-input">
                    <el-input v-model="filters.search" placeholder="Search..." @input="applySearch">
                        <i v-if="filters.search.length" slot="suffix" class="el-input__icon el-icon-error" @click="clearSearch"></i>
                    </el-input>
                </el-form-item>
            </el-form>
        </el-col>

        <!-- table -->
        <el-table
            v-if="$auth.check('roles.index')"
            v-loading="listLoading"
            :data="roles"
            highlight-current-row
            style="width: 100%;"
            @sort-change="handleSortChange">
            <el-table-column prop="display_name" label="Name" sortable="false"></el-table-column>
            <el-table-column prop="level" label="Level" width="90" sortable="false"></el-table-column>
            <el-table-column prop="description" label="Description" sortable="false" min-width="110px"></el-table-column>
            <el-table-column label="Actions" width="150" align="right">
                <template slot-scope="scope">
                    <el-tooltip
                        :disabled="!can('update', scope.row) || formVisible"
                        :open-delay="300"
                        placement="top"
                        content="Edit">
                        <span>
                            <el-button
                                :disabled="!can('update', scope.row)"
                                size="mini"
                                icon="el-icon-edit"
                                @click="handleEdit(scope.row)">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        :disabled="!can('delete', scope.row)"
                        :open-delay="300"
                        placement="top"
                        content="Delete">
                        <span>
                            <el-button
                                :disabled="!can('delete', scope.row)"
                                type="danger"
                                size="mini"
                                icon="el-icon-delete"
                                @click="handleDel(scope.row)">
                            </el-button>
                        </span>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
        <!--pagination-->
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
        <el-dialog :title="formTitle" :visible.sync="formVisible" :close-on-click-modal="false" :append-to-body="true">
            <el-form ref="form" :model="form" label-width="120px" :rules="rules" size="small" @submit.native.prevent="saveSubmit">
                <el-form-item label="Name" prop="display_name" :error="errors.get('display_name')">
                    <el-input
                        v-model="form.display_name"
                        suffix-icon="el-icon-edit"
                        placeholder="Role name"
                        @change="errors.clear('display_name')">
                    </el-input>
                </el-form-item>
                <el-form-item label="Description" prop="description" :error="errors.get('description')">
                    <el-input
                        v-model="form.description"
                        suffix-icon="el-icon-edit"
                        placeholder="Short role description"
                        @change="errors.clear('description')">
                    </el-input>
                </el-form-item>
                <el-form-item label="Access Level" prop="level" :error="errors.get('level')">
                    <el-input-number
                        v-model="form.level"
                        style="width: 50%;"
                        :min="($auth.user().roleLevel + 1)"
                        :max="255"
                        suffix-icon="el-icon-edit"
                        placeholder="Level"
                        @change="errors.clear('level')">
                    </el-input-number>

                    <el-tooltip
                        :content="'Minimal available level: ' + ($auth.user().roleLevel + 1)"
                        placement="top">
                        <i class="el-icon-info primary-text-color m-l-10"></i>
                    </el-tooltip>
                </el-form-item>
                <el-form-item label="Dashboard" prop="dashboard" :error="errors.get('dashboard')">
                    <el-select v-model="form.dashboard">
                        <el-option
                            key="default"
                            label="Default"
                            :value="null">
                        </el-option>
                        <el-option
                            v-for="dashboard in laravel.dashboards"
                            :key="dashboard"
                            :label="dashboard"
                            :value="dashboard">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Permissions" :error="errors.get('permissions')" class="permissions-list">
                    <el-tree
                        ref="permission_tree"
                        :data="permission_tree"
                        show-checkbox
                        node-key="id"
                        :props="defaultTreeProps">
                    </el-tree>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click.native="formVisible = false">
                    Cancel
                </el-button>
                <el-button type="success" :loading="formLoading" size="small" @click.native="saveSubmit">
                    Save
                </el-button>
            </div>
        </el-dialog>
    </section>
</template>

<style lang="scss">
    @import "../../../../sass/element-ui-colors";
    @import "../../../../sass/element-ui-variables";

    #roles {
        .permissions-list {
            .el-form-item__content {
                line-height: 20px;
            }
            .el-checkbox-group {
                margin-top: 10px;
                .el-checkbox {
                    font-weight: normal;
                }
            }
        }
        .search-input {
            float: right;
        }
    }
</style>

<script>
    import {methods}    from '../../../includes/_common_/util';
    import Errors       from '../../../includes/_common_/Errors';
    import axios        from 'axios';
    import Breadcrumb   from "../Breadcrumb";

    import {
        getRoleList,
        getRole,
        addRole,
        editRole,
        removeRole,
        getPermissionList
    } from '../../../includes/endpoints';

    const CancelToken = axios.CancelToken;

    export default {
        data() {
            return {
                laravel: Laravel,
                sortBy: 'id,asc',
                filters: {
                    search: '',
                },
                total: 0,
                page: 1,
                pageSize: 10,
                roles: [],
                permission_tree: [],
                errors: new Errors(),
                listLoading: true,
                listSource: CancelToken.source(),
                formVisible: false,
                formLoading: false,
                formTitle: 'New Role',
                form: {permission_ids: []},
                rules: {
                    display_name: [{required: true, message: 'name is required'}],
                    level: [{required: true, message: 'level is required'}],
                },
                defaultTreeProps: {
                    children: 'children',
                    label: 'label',
                    disabled: 'disabled',
                }
            };
        },
        methods: {
            ...methods,
            fetchPermissions() {
                getPermissionList().then((res) => {
                    this.getPermissionTree(res.data);
                });
            },
            handleSortChange(val) {
                if (val.prop != null) {
                    let sort = val.order.startsWith('a') ? 'asc' : 'desc';
                    this.sortBy = val.prop + ',' + sort;
                    this.fetchRoles();
                }
            },
            fetchRoles() {
                if (this.listSource) {
                    this.listSource.cancel('Fetch another users list');
                }
                this.listSource = CancelToken.source();
                let params = {
                    page: this.page,
                    pageSize: this.pageSize,
                    search: this.filters.search,
                    sortBy: this.sortBy,
                };
                this.listLoading = true;
                getRoleList(
                    params,
                    {
                        cancelToken: this.listSource.token
                    }
                ).then((res) => {
                    this.total = res.data.data.total;
                    this.roles = res.data.data.data;
                    this.listLoading = false;
                    this.listSource = false;
                });
            },
            handleDel(row) {
                this.$confirm('This will permanently delete the role. Continue?', 'Warning', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    removeRole(row.id).then((response) => {
                        this.$message.success(response.data.message);
                        if (this.roles.length === 1) {
                            // if we delete last item on page
                            // reduce total to update pagination
                            this.total--;
                            this.listLoading = false;
                        } else {
                            this.fetchRoles();
                        }
                    }).catch((error) => {
                        if (Laravel.appDebug) {
                            console.log(error);
                        }
                        if (error.response.data.message) {
                            this.$message.error(error.response.data.message);
                        } else {
                            this.$message.error('Unknown server error');
                        }
                        this.listLoading = false;
                    });
                }).catch(() => {
                });
            },
            handleEdit(row) {
                this.formTitle = 'Edit Role';
                this.form = Object.assign({}, row);
                this.errors = new Errors();
                this.updateTree(this.$auth.user().roleList.includes(this.form.name));
                this.formVisible = true;
                if (this.$refs['form']) {
                    this.$refs['form'].resetFields();
                }
                if (this.$refs['permission_tree']) {
                    this.$refs['permission_tree'].setCheckedKeys(row.permission_ids);
                } else {
                    this.$nextTick(function () {
                        this.$refs['permission_tree'].setCheckedKeys(row.permission_ids);
                    });
                }
            },
            handleAdd() {
                this.formTitle = 'New Role';
                this.form = {permission_ids: []};
                this.errors = new Errors();
                this.formVisible = true;
                this.updateTree(false);
                if (this.$refs['form']) {
                    this.$refs['form'].resetFields();
                }
                if (this.$refs['permission_tree']) {
                    this.$refs['permission_tree'].setCheckedKeys([]);
                }
            },
            saveSubmit() {
                this.formLoading = true;
                let action = this.form.id ? editRole : addRole;
                this.$set(this.form, 'permission_ids', this.$refs['permission_tree'].getCheckedKeys(true));
                action(this.form).then((response) => {
                    this.formLoading = false;
                    this.$message.success(response.data.message);
                    this.formVisible = false;
                    this.fetchRoles();
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
            },
            applySearch: _.debounce(function (e) {
                this.fetchRoles();
            }, 300),
            clearSearch() {
                this.filters.search = '';
                this.fetchRoles();
            },
            getPermissionTree(permissions) {
                let tree = [];
                permissions.map(function (permission) {
                    permission.name = permission.name.split('.');
                    permission.display_name = permission.display_name.split(' ');
                    return permission;
                });
                let copy = permissions.slice();
                _.forEach(permissions, function (permission) {
                    let found = _.filter(copy, function (c) {
                        return c.name[0] === permission.name[0];
                    });
                    copy = _.reject(copy, function (c) {
                        return c.name[0] === permission.name[0];
                    });
                    if (found.length > 1) {
                        found = found.map(function (f) {
                            return {
                                id: f.id,
                                label: f.display_name.slice(1).join(' '),
                                disabled: false,
                            };
                        });
                        found.sort(function (a, b) {
                            let nameA = a.label.toUpperCase();
                            let nameB = b.label.toUpperCase();
                            return nameA < nameB ? -1 : (nameA > nameB ? 1 : 0);
                        });
                        let node = {
                            label: permission.display_name[0],
                            children: found,
                            disabled: false
                        };
                        tree.push(node);
                    } else if (found.length === 1) {
                        let node = {
                            id: permission.id,
                            label: permission.display_name.join(' '),
                            disabled: false
                        };
                        tree.push(node);
                    }
                });
                this.permission_tree = tree;
            },
            updateTree(disabled) {
                disabled = !!disabled;
                this.permission_tree.map(function (p) {
                    p.disabled = disabled;
                    if(p.children) {
                        p.children.map(function (c) {
                            c.disabled = disabled;
                        });
                    }
                });
            },
            can(action, row) {
                switch (action) {
                    case 'update':
                        return this.$auth.check('roles.update')
                            && (this.$auth.user().roleLevel < row.level);
                    case 'delete':
                        return this.$auth.check('roles.delete')
                            && (this.$auth.user().roleLevel < row.level)
                            && !row.initial;
                    default:
                        return false;
                }
            }
        },
        watch: {
            page: function (val) {
                this.fetchRoles();
            },
            pageSize: function (val) {
                this.fetchRoles();
            },
        },
        mounted() {
            this.fetchRoles();
            this.fetchPermissions();
            this.listenSearchEvent(getRole, 'Roles');
        },
        components: {
            Breadcrumb,
        }
    };
</script>

