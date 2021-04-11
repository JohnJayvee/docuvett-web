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
            :visible.sync="dialogVisible"
            width="400px">
            <h2>Title in wrong place</h2>
            <span>Wrong size and footer buttons missing</span>
        </el-dialog>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        name: "UIDialogStructureBad",
        components:{
        },
        props: [],
        data() {
            return {
                filters: {
                    search: ''
                },
                dialogVisible: false
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

