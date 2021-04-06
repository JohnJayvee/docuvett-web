<template>
    <section>
        <detail-header></detail-header>
        <el-row :gutter="50">
            <el-col :span="6">
                <el-tabs value="first">
                    <el-tab-pane label="Client" name="first">
                    </el-tab-pane>
                </el-tabs>
            </el-col>
            <el-col :span="18">
                <el-tabs value="first">
                    <el-tab-pane label="Job Card" name="first">
                        <h3>List of forms</h3>
                        <el-collapse value="1" accordion>
                            <el-collapse-item title="Form 1" name="1">
                                <el-form label-position="right" label-width="120px" :model="form" size="small">
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
                            </el-collapse-item>
                            <el-collapse-item title="Form 2" name="2">
                                <el-form label-position="right" label-width="120px" :model="form" size="small">
                                    <el-form-item label="Other information">
                                        <el-input v-model="form.other"></el-input>
                                    </el-form-item>
                                </el-form>
                            </el-collapse-item>
                            <el-collapse-item title="Form 3" name="3">
                                <el-form label-position="right" label-width="120px" :model="form" size="small">
                                    <el-form-item label="Other information">
                                        <el-input v-model="form.other"></el-input>
                                    </el-form-item>
                                </el-form>
                            </el-collapse-item>
                        </el-collapse>
                    </el-tab-pane>
                    <el-tab-pane label="Job Pieces" name="second">
                    </el-tab-pane>
                    <el-tab-pane label="Paint Calculator" name="three">
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
        name: "UIDetailHeaderGood",
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

