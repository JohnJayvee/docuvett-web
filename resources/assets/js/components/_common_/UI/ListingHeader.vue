<template>
    <section>
        <el-row>
            <h2 class="page-title">
                Products
            </h2>
        </el-row>
        <breadcrumb></breadcrumb>

        <!-- button & search -->
        <el-row>
            <el-col :span="24" class="m-t-10">
                <el-form :inline="true" :model="filters" size="mini" @submit.native.prevent="fetchProducts">
                    <el-form-item>
                        <el-button type="primary" icon="el-icon-plus" @click="handleAdd">
                            Add
                        </el-button>
                    </el-form-item>
                    <el-form-item class="search-input pull-right">
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
        </el-row>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    import Breadcrumb from "../../../views/Admin/Breadcrumb";

    export default {
        name: "UIListingHeader",
        components:{
            Breadcrumb,
        },
        props: [],
        data() {
            return {
                filters: {
                    search: ''
                },
            };
        },
        mounted() {
        },
        methods: {
            handleAdd() {

            },
            applySearch() {

            },
            clearSearch() {

            },
        }
    };
</script>

