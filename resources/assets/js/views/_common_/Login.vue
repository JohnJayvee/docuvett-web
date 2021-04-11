<template>
    <el-container class="login-screen background-image">
        <app-header></app-header>
        <el-main class="login-section">
            <div class="v-align-container">
                <div class="t-cell">
                    <el-form v-show="!showTwoFA" label-position="left" label-width="0px" class="login-container">
                        <div class="title">
                            Sign In
                        </div>

                        <el-form-item prop="email" :error="errors.get('email')">
                            <el-input
                                ref="email"
                                v-model="loginForm.email"
                                type="email"
                                auto-complete="on"
                                placeholder="Email"
                                suffix-icon="el-icon-message"
                                @change="errors.clear('email')"></el-input>
                        </el-form-item>

                        <el-form-item prop="password" :error="errors.get('password')">
                            <el-input
                                v-model="loginForm.password"
                                type="password"
                                auto-complete="on"
                                placeholder="Password"
                                suffix-icon="el-icon-fa-key"
                                @change="errors.clear('password')"></el-input>
                        </el-form-item>

                        <el-checkbox v-model="checked" checked class="remember">
                            Remember password
                        </el-checkbox>

                        <el-form-item style="width:100%;" :error="errors.get('common')">
                            <el-button
                                type="primary"
                                style="width:100%;"
                                :loading="isSubmitting"
                                native-type="submit"
                                icon="el-icon-fa-sign-in"
                                @click.native.prevent="submit">
                                Login
                            </el-button>
                        </el-form-item>
                        <div class="text-center">
                            <el-button type="text" style="padding-bottom: 0;" @click.native="handleForgotPassword">
                                Forgot password?
                            </el-button>
                        </div>
                    </el-form>
                    <el-form v-show="showTwoFA" label-position="top" class="login-container verification-container">
                        <!-- Close button for 2FA Verification -->
                        <button type="button" class="close-verification el-dialog__headerbtn" aria-label="Close" @click="handleTwoFaCloseModal">
                            <i class="el-dialog__close el-icon el-icon-close"></i>
                        </button>
                        <div class="title">
                            2-Step Verification
                        </div>
                        <el-form-item prop="code" :error="errors.get('code')">
                            <div slot="label" class="label">
                                Authentication code
                            </div>
                            <el-input
                                ref="code"
                                v-model="loginForm.code"
                                type="text"
                                auto-complete="on"
                                placeholder="code"
                                suffix-icon="el-icon-phone-outline"
                                @change="errors.clear('code')">
                            </el-input>
                        </el-form-item>
                        <el-form-item style="width:100%; margin-bottom: 12px;" :error="errors.get('common')">
                            <el-button
                                type="primary"
                                style="width:100%;"
                                :loading="isSubmitting"
                                native-type="submit"
                                icon="el-icon-fa-sign-in"
                                @click.native.prevent="submit">
                                Verify
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <el-dialog
                    title="Reset Password"
                    :visible.sync="resetFormVisible"
                    :close-on-click-modal="false"
                    :append-to-body="true"
                    width="420px">
                    <el-form ref="resetForm" :model="resetForm" label-width="0px" :rules="resetRules" @submit.native.prevent="resetSubmit">
                        <el-form-item prop="email" :error="resetErrors.get('email')">
                            <el-input
                                v-model="resetForm.email"
                                type="email"
                                auto-complete="on"
                                placeholder="Your email address"
                                suffix-icon="el-icon-message"
                                @change="errors.clear('email')">
                            </el-input>
                        </el-form-item>
                    </el-form>
                    <br>
                    <div class="text-right">
                        <el-button :disabled="resetFormLoading" @click="resetFormVisible = false">
                            Cancel
                        </el-button>
                        <el-button
                            type="primary"
                            :loading="resetFormLoading"
                            native-type="submit"
                            @click.native.prevent="resetSubmit">
                            Send Reset Email
                        </el-button>
                    </div>
                </el-dialog>
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
            min-height: 600px;
            @media (max-width: $--xs - 1) {
                min-height: 500px;
            }
        }
        .login-section {
            padding-top: 140px;
            padding-bottom: 0;
            min-height: 600px;
            display: flex;
            align-items: center;
            align-content: center;
            @media (max-width: $--xs - 1) {
                min-height: 500px;
                padding-top: 80px;
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
            .remember {
                margin: 0 0 22px 0;
            }
            @media (max-width: $--xs - 1) {
                padding: 15px;
            }
        }

    }
</style>

<script>
    import Errors from '../../includes/_common_/Errors';
    import appHeader from '../../components/_common_/Header';
    import {methods} from '~/includes/_common_/util';

    export default {
        components: {
            'app-header': appHeader,
        },
        data() {
            return {
                isSubmitting: false,
                loginForm: {
                    email: '',
                    password: '',
                    code: ''
                },
                dontSendCodeField: true,
                showTwoFA: false,
                errors: new Errors(),
                checked: true,
                resetFormVisible: false,
                resetFormLoading: false,
                resetForm: {
                    email: '',
                },
                resetRules: {
                    email: [{required: true, type: 'email'}],
                },
                resetErrors: new Errors(),
                offline: (window.navigator && !window.navigator.onLine),
            };
        },
        mounted() {
            this.$refs['email'].focus();
            window.addEventListener('online', () => {
                this.offline = false;
            });
            window.addEventListener('offline', () => {
                this.offline = true;
            });
        },
        methods: {
            ...methods,
            submit() {
                if (!this.isSubmitting) {

                    if (this.offline) {
                        this.$message({
                            type: 'error',
                            message: 'Network connection is not available',
                        });
                    }else {
                        this.isSubmitting = true;
                        let formData = {
                            email: this.loginForm.email,
                            password: this.loginForm.password
                        };
                        if (!this.dontSendCodeField) {
                            _.assign(formData, {code: this.loginForm.code});
                        }
                        this.$auth.login({
                            data: formData,
                            rememberMe: this.checked,
                            redirect: {name: 'Dashboard', params: {
                                checkSubscriptionTrial: false
                            }},
                            error(error) {
                                if (error.response && error.response.data) {
                                    let data = error.response.data;
                                    if (data.errors) {
                                        if (data.errors.code && !this.showTwoFA) {
                                            this.showTwoFA = true;
                                            this.loginForm.code = '';
                                            this.dontSendCodeField = false;
                                            this.$message.success(data.errors.code[0]);
                                        } else {
                                            if (data.errors.code && data.errors.code.length > 1) {
                                                this.$message.success(data.errors.code[1]);
                                            }
                                            this.errors.record(data.errors);
                                        }
                                    } else if (data.error) {
                                        this.$message({
                                            type: 'error',
                                            dangerouslyUseHTMLString: true,
                                            message: data.error,
                                        });
                                    } else {
                                        location.reload();
                                    }
                                }
                                this.isSubmitting = false;
                            }
                        }).then((response) => {
                            if (response.data.system_messages) {
                                this.fetchSystemMessages();
                            }
                        });
                    }
                }
            },
            handleForgotPassword() {
                this.resetErrors.clear(null);
                if (this.$refs['resetForm']) {
                    this.$refs['resetForm'].resetFields();
                }
                this.resetForm.email = this.loginForm.email;
                this.resetFormVisible = true;
            },
            handleTwoFaCloseModal () {
                this.showTwoFA = false;
                this.dontSendCodeField = true;
            },
            resetSubmit() {
                this.resetErrors.clear(null);
                this.$refs['resetForm'].validate((valid) => {
                    if (valid && !this.resetFormLoading) {
                        this.resetFormLoading = true;
                        alert('Not implemented');
                        return 0;
                        /*sendResetEmail(this.resetForm).then(response => {
                            this.resetFormLoading = false;
                            this.resetFormVisible = false;
                            this.$message.success(response.data.message);
                        }).catch(error => {
                            if (this.offline) {
                                this.$message({
                                    type: 'error',
                                    message: 'Network connection is not available',
                                });
                            } else if (error.response && error.response.data) {
                                if (error.response.data.errors) {
                                    this.errors.record(error.response.data.errors);
                                } else if (error.response.data.message) {
                                    this.$message.error(error.response.data.message);
                                } else {
                                    this.$message.error('Unknown server error');
                                }
                            }
                            this.resetFormLoading = false;
                        });*/
                    } else {
                        return false;
                    }
                });
            },
        },
    };
</script>
