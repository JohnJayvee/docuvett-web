<template>
    <section>
        <el-row>
            <el-col>
                <el-button type="text" @click="dialogVisible = true">
                    click to open the Dialog
                </el-button>
            </el-col>
        </el-row>

        <el-dialog
            title="Smaller Dialog"
            :visible.sync="dialogVisible"
            width="600px">
            <el-row :gutter="20">
                <el-col :span="24">
                    <el-tabs value="first">
                        <el-tab-pane label="Form" name="first">
                            <el-form ref="form" :model="form" label-width="120px" size="small">
                                <el-form-item label="Activity name">
                                    <el-input v-model="form.name"></el-input>
                                </el-form-item>
                                <el-form-item label="Activity zone">
                                    <el-select v-model="form.region" placeholder="please select your zone">
                                        <el-option label="Zone one" value="shanghai"></el-option>
                                        <el-option label="Zone two" value="beijing"></el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="Activity time">
                                    <el-col :span="11">
                                        <el-date-picker v-model="form.date1" type="date" placeholder="Pick a date" style="width: 100%;"></el-date-picker>
                                    </el-col>
                                    <el-col class="line" :span="2">
                                        -
                                    </el-col>
                                    <el-col :span="11">
                                        <el-time-picker v-model="form.date2" placeholder="Pick a time" style="width: 100%;"></el-time-picker>
                                    </el-col>
                                </el-form-item>
                                <el-form-item label="Activity type">
                                    <el-checkbox-group v-model="form.type">
                                        <el-checkbox label="Online activities" name="type"></el-checkbox>
                                        <el-checkbox label="Promotion activities" name="type"></el-checkbox>
                                        <el-checkbox label="Offline activities" name="type"></el-checkbox>
                                        <el-checkbox label="Simple brand exposure" name="type"></el-checkbox>
                                    </el-checkbox-group>
                                </el-form-item>
                                <el-form-item label="Resources">
                                    <el-radio-group v-model="form.resource">
                                        <el-radio label="Sponsor"></el-radio>
                                        <el-radio label="Venue"></el-radio>
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="Activity form">
                                    <el-input v-model="form.desc" type="textarea"></el-input>
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </el-col>
            </el-row>
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="dialogVisible = false">Cancel</el-button>
                <el-button size="small" type="success" @click="dialogVisible = false">Save</el-button>
            </span>
        </el-dialog>
    </section>
</template>

<style lang="scss" scoped>
</style>

<script>
    export default {
        name: "UIResponsivenessDialog",
        components:{
        },
        props: [],
        data() {
            return {
                filters: {
                    search: ''
                },
                dialogVisible: false,
                form: {
                    name: '',
                    region: '',
                    date1: '',
                    date2: '',
                    delivery: false,
                    type: [],
                    resource: '',
                    desc: ''
                },
                tableData: []
            };
        },
        mounted() {
        },
        methods: {
            handleAdd() {

            },
            handleClose() {

            },
            applySearch() {

            },
            clearSearch() {

            },
        }
    };
</script>

