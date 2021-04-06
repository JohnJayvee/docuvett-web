<template>
    <el-container class="login-screen background-image">
        <app-header></app-header>
        <el-main class="login-section">
            <div class="v-align-container">
                <div class="t-cell">
                    <el-form ref="form" label-position="left" label-width="0px" class="login-container" :model="form" :rules="rules">
                        <div class="title">
                            Reset Password
                        </div>
                        <el-form-item prop="password" :error="errors.get('password')">
                            <el-input
                                v-model="form.password"
                                type="password"
                                suffix-icon="el-icon-fa-key"
                                placeholder="Enter new password"
                                @change="errors.clear('password')">
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="password_confirmation" :error="errors.get('password_confirmation')">
                            <el-input
                                v-model="form.password_confirmation"
                                type="password"
                                suffix-icon="el-icon-fa-key"
                                placeholder="Confirm new password"
                                @change="errors.clear('password_confirmation')">
                            </el-input>
                        </el-form-item>
                        <el-form-item style="width:100%;" :error="errors.get('common')">
                            <el-button
                                type="primary"
                                style="width:100%;"
                                :loading="isSubmitting"
                                native-type="submit"
                                icon="el-icon-fa-sign-in"
                                @click.native.prevent="submit">
                                Reset
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-main>
    </el-container>
</template>

<style lang="scss" scoped>
    @import "../../../sass/element-ui-colors";
    @import "../../../sass/element-ui-variables";

    .login-screen {
        min-height: 100%;
        &.background-image {
            background-image: url("../../../img/header-image.jpg");
            background-size: cover;
            background-position: center;

            &:before {
                background-image: repeating-radial-gradient(circle at center, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4) 2px, transparent 2px, transparent 100%);
                background-size: 6px 6px;
            }
        }
        &:before {
            content: '';
            position: absolute;
            height: 100%;
            width: 100%;
            left: 0;
            min-height: 550px;
            @media (max-width: $--xs - 1) {
                min-height: 500px;
            }
        }
        .login-section {
            padding-top: 140px;
            padding-bottom: 0;
            min-height: 550px;
            display: flex;
            align-items: center;
            align-content: center;
            @media (max-width: $--xs - 1) {
                padding-top: 80px;
                min-height: 500px;
            }

        }

        .v-align-container {
            width: 100%;
        }

        .login-container {
            /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
            -webkit-border-radius: 5px;
            border-radius: 5px;
            -moz-border-radius: 5px;
            background-clip: padding-box;
            margin: 0 auto;
            position: relative;
            top: 10%;
            /*align-items: center;*/
            min-width: 170px;
            max-width: 350px;
            padding: 34px;
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid $--border-color-base;
            box-shadow: $--box-shadow-light;
            .title {
                margin-bottom: 18px;
                margin-top: 0;
                font-size: 22px;
                text-align: center;
                color: $--color-text-primary;
            }
            @media (max-width: $--xs - 1) {
                padding: 15px;
            }
        }

    }
</style>

<script>
    import Errors from '../../includes/_common_/Errors';
    import {methods} from '~/includes/_common_/util';
    import appHeader from '../../components/_common_/Header';

    export default {
        components: {
            'app-header': appHeader,
        },
        data() {
            return {
                isSubmitting: false,
                form: {
                    email: '',
                    token: ''
                },
                errors: new Errors(),
                rules: {
                    password: [{required: true}],
                    password_confirmation: [{required: true, message: 'confirmation is required'}]
                },
            };
        },
        mounted() {
            this.form.email = this.getQueryStringByName('email');
            this.form.token = this.getQueryStringByName('token');
        },
        methods: {
            ...methods,
            submit() {
                this.errors.clear(null);
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        this.isSubmitting = true;
                        alert('Not implemented');
                        return 0;
                        /*passwordReset(this.form).then(response => {
                            this.isSubmitting = false;
                            this.$message.success(response.data.message);
                            this.$router.push({path: '/login'});
                        }).catch(error => {
                            if (error.response.data) {
                                if (error.response.data.errors) {
                                    this.errors.record(error.response.data.errors);
                                } else if (error.response.data.message) {
                                    this.$message.error(error.response.data.message);
                                } else {
                                    this.$message.error('Unknown server error');
                                }
                            }
                            this.isSubmitting = false;
                        });*/
                    } else {
                        return false;
                    }
                });
            }
        },
    };
</script>
