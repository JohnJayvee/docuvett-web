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
            title="Tips"
            :visible.sync="dialogVisible"
            width="30%">
            <span>This is a message</span>
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
        name: "UIDialogStructure",
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

