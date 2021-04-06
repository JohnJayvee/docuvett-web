<template>
    <section>
        <!-- table -->
        <el-table
            ref="table"
            v-loading="listLoading"
            :data="data"
            highlight-current-row
            stripe
            @filter-change="handleFilterChange"
            @sort-change="handleSortChange">
            <el-table-column key="table-name" label="Name" prop="name" sortable>
                <template slot-scope="scope">
                    {{ scope.row.name }}
                </template>
            </el-table-column>
            <el-table-column key="table-actions" label="Actions" align="right" prop="actions">
                <template>
                    <el-tooltip
                        effect="dark"
                        content="Edit"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-edit"
                                size="mini">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        effect="dark"
                        content="Primary"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-help"
                                size="mini"
                                type="primary">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        effect="dark"
                        content="Success"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-check"
                                size="mini"
                                type="success">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        effect="dark"
                        content="Info"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-magic-stick"
                                size="mini"
                                type="info">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        effect="dark"
                        content="Warning"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-files"
                                size="mini"
                                type="warning"
                                @click="handleConfirmation()">
                            </el-button>
                        </span>
                    </el-tooltip>
                    <el-tooltip
                        effect="dark"
                        content="Delete"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-delete"
                                size="mini"
                                type="danger"
                                @click="handleConfirmation()">
                            </el-button>
                        </span>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    import {} from "~/includes/Admin/endpoints";

    export default {
        name: "UIListingTableActions",
        components:{
        },
        props: [],
        data() {
            return {
                data: [
                    {
                        name: 'John Doe',
                        email: 'johndoe@email.com',
                        phone: '+61755555555',
                    }
                ],
                listLoading: false,
                total: 50,
                pageSize: 10,
                page: 1
            };
        },
        mounted() {
        },
        methods: {
            handleConfirmation() {
                this.$confirm(`You are about to do something dangerous. Continue?`, 'Warning', {
                    type: 'warning'
                }).then(() => {

                });
            }
        }
    };
</script>

