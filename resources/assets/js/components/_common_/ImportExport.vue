<template>
    <el-form-item class="pull-right" :class="className">
        <el-form-item class="pull-right m-b-0">
            <el-button :disabled="!canExport" icon="el-icon-download" :loading="exportLoading" @click="handleExport">
                Export
            </el-button>
        </el-form-item>
        <el-form-item class="pull-right m-b-0">
            <el-button :disabled="!canImport" icon="el-icon-upload2" @click="toggleImportForm">
                Import
            </el-button>
        </el-form-item>

        <!-- import dialog -->
        <el-dialog :title="'Import ' + pageName" :visible.sync="importFormVisible" :append-to-body="true" class="import-dialog">
            <div class="text-center">
                <el-upload
                    ref="upload"
                    drag
                    action="/"
                    :http-request="handleImport"
                    :auto-upload="false"
                    accept=".csv, .xls, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">
                        Drop file here or <em>click to upload</em>
                    </div>
                    <div slot="tip" class="el-upload__tip">
                        Supported formats: .xlsx / .xls / .csv
                    </div>
                    <div slot="tip" class="el-upload__tip">
                        Valid columns: [{{ validColumns }}]
                    </div>
                </el-upload>
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click.native="toggleImportForm">
                    Cancel
                </el-button>
                <el-button style="margin-left: 10px;" type="success" :loading="importFormLoading" size="small" @click="submitUpload">
                    Import
                </el-button>
            </div>
        </el-dialog>
    </el-form-item>
</template>

<script>
    export default {
        props: [
            'exportUrl',
            'importUrl',
            'canImport',
            'canExport',
            'validColumnsForImport',
            'pageName',
            'className',
            'paramsExtra'
        ],
        data() {
            return {
                importFormLoading: false,
                importFormVisible: false,
                exportLoading: false,
                export: this.exportUrl,
                import: this.importUrl,
                validColumns: this.validColumnsForImport,
            };
        },
        methods: {
            toggleImportForm() {
                if (this.$refs.upload) {
                    this.$refs.upload.clearFiles();
                }
                this.importFormVisible = !this.importFormVisible;
            },
            submitUpload() {
                this.$refs.upload.submit();
            },
            handleImport(event) {
                this.importFormLoading = true;
                const data = new FormData();
                data.append('file', event.file);
                this.import(data).then((response) => {
                    this.importFormLoading = false;
                    this.importFormVisible = false;
                    this.$emit('importSuccess');
                }).catch((error) => {
                    this.importFormLoading = false;
                    if (Laravel.appDebug) {
                        console.log(error);
                    }
                    if (error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Unknown server error');
                    }
                });
            },
            handleExport() {
                let params = {
                    search: '',
                    sortBy: 'id,asc'
                };
                if(this.paramsExtra && Object.keys(this.paramsExtra).length !== 0) {
                    params = Object.assign(params, this.paramsExtra);
                }
                this.exportLoading = true;
                this.export(params).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'export_' + this.pageName + '_' + moment().format('YYYY_MM_DD') + '.xlsx'); // .csv works as well, but gives a warning when opening in excel
                    document.body.appendChild(link);
                    link.click();
                    this.exportLoading = false;
                });
            },
        },
    };
</script>

<style lang="scss">
    .import-dialog {
        .el-upload {
            display: block;

            .el-upload-dragger {
                width: 100%;
            }
        }
    }
</style>