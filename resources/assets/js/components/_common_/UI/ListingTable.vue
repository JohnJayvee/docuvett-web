<template>
    <section>
        <!-- table header -->
        <el-row>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" class="m-b-15">
                <el-select
                    v-model="groupBy"
                    class="m-r-15"
                    size="mini"
                    placeholder="Select..."
                    @change="handleGroupByChange()">
                    <el-option
                        v-for="item in filterByVariants"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                    </el-option>
                </el-select>
            </el-col>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" class="prev-next-btns m-b-15">
                <span class="pull-right" @click="handleWeekChange(1)">
                    Next Week
                    <i class="el-icon-arrow-right"></i>
                </span>
                <span class="m-r-20 pull-right">
                    <el-date-picker
                        v-model="weekDayPicker"
                        :clearable="false"
                        type="week"
                        format="'From' d MMM"
                        placeholder="Pick a week"
                        size="mini"
                        style="width: 140px"
                        @change="handlePickerWeekChange">
                    </el-date-picker>
                </span>
                <span class="m-r-20 pull-right" @click="handleWeekChange(-1)">
                    <i class="el-icon-arrow-left"></i>
                    Prev Week
                </span>
            </el-col>
        </el-row>

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
            <el-table-column key="table-email" label="Email" prop="email" sortable>
                <template slot-scope="scope">
                    <el-button
                        :data-email="scope.row.email"
                        :data-name="scope.row.name"
                        type="text">
                        {{ scope.row.email }}
                    </el-button>
                </template>
            </el-table-column>
            <el-table-column key="table-phone" label="Phone" prop="phone" sortable>
                <template slot-scope="scope">
                    {{ scope.row.phone }}
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Pending"
                        placement="top">
                        <span class="validation-status">
                            <i class="pending"></i>
                        </span>
                    </el-tooltip>
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
                        content="Delete"
                        placement="top">
                        <span>
                            <el-button
                                icon="el-icon-delete"
                                size="mini"
                                type="danger">
                            </el-button>
                        </span>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>

        <!-- pagination -->
        <el-row>
            <el-col :span="24" class="el-pagination">
                <el-pagination
                    layout="sizes, prev, pager, next"
                    :current-page.sync="page"
                    :page-size.sync="pageSize"
                    :total="total"
                    class="pull-right">
                </el-pagination>
            </el-col>
        </el-row>
    </section>
</template>

<style lang="scss" scoped>
  .prev-next-btns {
    font-size: 12px;
    line-height: 28px;
  }
</style>

<script>
    import moment from "moment";

    export default {
        name: "UIListingTable",
        components:{
        },
        props: [],
        data() {
            return {
                filters: {
                    search: ''
                },
                filterByVariants: [
                    {
                        label: 'Filter by Name',
                        value: 'site'
                    },
                    {
                        label: 'Filter by State',
                        value: 'state'
                    },
                    {
                        label: 'Filter by Locality',
                        value: 'locality'
                    }
                ],
                groupBy : 'state',
                weekDayPicker: moment(),
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
            handleGroupByChange() {

            },
            handleWeekChange() {

            },
            handlePickerWeekChange() {

            },
            handleFilterChange() {

            },
            handleSortChange() {

            }
        }
    };
</script>

