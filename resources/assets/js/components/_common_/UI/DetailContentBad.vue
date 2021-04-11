<template>
    <section>
        <detail-header></detail-header>
        <el-row :gutter="50">
            <el-col :span="18">
                <el-form label-position="top" label-width="120px" :model="form" size="small">
                    <el-form-item label="Name">
                        <el-input v-model="form.name"></el-input>
                    </el-form-item>
                    <el-form-item label="Activity zone">
                        <el-input v-model="form.region"></el-input>
                    </el-form-item>
                    <el-form-item label="Activity form">
                        <el-input v-model="form.type"></el-input>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span="6">
                <el-tabs value="none">
                    <el-tab-pane label="Job Card" name="first">
                    </el-tab-pane>
                </el-tabs>
            </el-col>
        </el-row>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    import DetailHeader from "./DetailHeader.vue";

    export default {
        name: "UIDetailHeader",
        components:{
            DetailHeader
        },
        props: [],
        data() {
            return {
                filters: {
                    search: ''
                },
                form: {
                    name: 'John Doe',
                    region: 'Brisbane',
                    type: 'Prospect'
                }
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

