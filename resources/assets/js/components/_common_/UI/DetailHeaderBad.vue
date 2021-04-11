<template>
    <section>
        <el-row>
            <h2 class="page-title">
                Job Cards:
            </h2>
        </el-row>
        <breadcrumb></breadcrumb>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    import Breadcrumb from "../../../views/Admin/Breadcrumb";

    export default {
        name: "UIDetailHeaderBad",
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

