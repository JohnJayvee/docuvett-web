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
                        <el-row>
                            <el-col :span="24">
                                <div class="pull-right m-b-15">
                                    <el-button size="small">
                                        Cancel
                                    </el-button>
                                    <el-button size="small" type="success">
                                        Save
                                    </el-button>
                                </div>
                            </el-col>
                        </el-row>
                        <el-collapse value="0" accordion>
                            <el-collapse-item title="Consistency" name="1">
                                <div>Consistent with real life: in line with the process and logic of real life, and comply with languages and habits that the users are used to;</div>
                                <div>Consistent within interface: all elements should be consistent, such as: design style, icons and texts, position of elements, etc.</div>
                            </el-collapse-item>
                            <el-collapse-item title="Feedback" name="2">
                                <div>Operation feedback: enable the users to clearly perceive their operations by style updates and interactive effects;</div>
                                <div>Visual feedback: reflect current state by updating or rearranging elements of the page.</div>
                            </el-collapse-item>
                            <el-collapse-item title="Efficiency" name="3">
                                <div>Simplify the process: keep operating process simple and intuitive;</div>
                                <div>Definite and clear: enunciate your intentions clearly so that the users can quickly understand and make decisions;</div>
                                <div>Easy to identify: the interface should be straightforward, which helps the users to identify and frees them from memorizing and recalling.</div>
                            </el-collapse-item>
                            <el-collapse-item title="Controllability" name="4">
                                <div>Decision making: giving advices about operations is acceptable, but do not make decisions for the users;</div>
                                <div>Controlled consequences: users should be granted the freedom to operate, including canceling, aborting or terminating current operation.</div>
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
        name: "UIDetailContentButtonsBad",
        components:{
            DetailHeader
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

