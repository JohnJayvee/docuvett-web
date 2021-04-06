<template>
    <el-container class="login-screen background-image">
        <app-header></app-header>
        <el-main class="login-section">
            <div class="v-align-container">
                <div class="t-cell">
                    <el-form ref="form" label-position="left" label-width="0px" class="login-container" :model="form" :rules="rules" @submit.native.prevent="submit">
                        <div class="title">
                            Sign Up Now
                        </div>
                        <el-form-item prop="name" :error="errors.get('name')">
                            <el-input v-model="form.name" auto-complete="on" placeholder="Full Name" suffix-icon="el-icon-edit" @change="errors.clear('name')"></el-input>
                        </el-form-item>
                        <el-form-item prop="email" :error="errors.get('email')">
                            <el-input ref="email-input" v-model="form.email" auto-complete="on" type="email" placeholder="Email" @change="errors.clear('email')">
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="phone" :error="errors.get('phone')">
                            <vue-tel-input
                                ref="phone-input"
                                v-model="form.phone"
                                default-country="AU"
                                :preferred-countries="['AU']"
                                :input-options="{ showDialCode: true}"
                                auto-complete="on"
                                placeholder="Mobile phone number"
                                suffix-icon="el-icon-fa-phone"
                                type="phone"
                                @change="errors.clear('phone')"
                                @input="handlePhoneInput">
                            </vue-tel-input>
                        </el-form-item>
                        <el-form-item prop="password" :error="errors.get('password')">
                            <el-row>
                                <el-col :span="11">
                                    <el-input
                                        v-model="form.password"
                                        type="password"
                                        suffix-icon="el-icon-fa-key"
                                        placeholder="Enter password"
                                        @change="errors.clear('password')">
                                    </el-input>
                                </el-col>
                                <el-col :span="11" :offset="2">
                                    <el-input
                                        v-model="form.password_confirmation"
                                        type="password"
                                        suffix-icon="el-icon-fa-key"
                                        placeholder="Confirm password">
                                    </el-input>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item class="m-b-0" style="width:100%;" :error="errors.get('common')">
                            <el-button
                                type="primary"
                                style="width:100%;"
                                :loading="isSubmitting"
                                native-type="submit"
                                icon="el-icon-fa-sign-in"
                                @click.native.prevent="submit">
                                Register
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-main>
    </el-container>
</template>

<style lang="scss">
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
            min-height: 750px;
            @media (max-width: $--xs - 1) {
                min-height: 680px;

            }
        }
        .login-section {
            padding-top: 140px;
            padding-bottom: 0;
            min-height: 750px;
            display: flex;
            align-items: center;
            align-content: center;
            @media (max-width: $--xs - 1) {
                padding-top: 80px;
                min-height: 680px;

            }

        }
        .relative {
            position: relative;
        }
        .float-left {
            float: left !important;
        }
        .clearfix:after {
            clear: both;
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
                .el-radio + .el-radio {
                    margin-left: 20px;
                }
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
                form: {},
                errors: new Errors(),
                checked: true,
                rules: {
                    name: [{required: true}],
                    email: [{required: true, type: 'email'}],
                    password: [{required: true}],
                    phone: [{required: true}, {validator: this.validatePhoneNumber}],
                },
                phoneCodeSending: false,
                emailCodeSending: false,
                codeSent: false,
            };
        },
        methods: {
            ...methods,
            submit() {
                this.errors.clear(null);
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        this.isSubmitting = true;
                        this.$auth.register({
                            data: this.form,
                            autoLogin: false,
                            success(response) {
                                // noinspection JSPotentiallyInvalidUsageOfThis
                                this.isSubmitting = false;
                                this.$message.success({
                                    showClose: true,
                                    message: response.data.message,
                                    position: 'top-center'
                                });
                                this.$router.push({path: '/login'});
                            },
                            error(error) {
                                if (error.response.data) {
                                    if (error.response.data.errors) {
                                        this.errors.record(error.response.data.errors);
                                    } else if (error.response.data.message) {
                                        this.$message.error(error.response.data.message);
                                    } else {
                                        this.$message.error('Unknown server error');
                                    }
                                }
                                // noinspection JSPotentiallyInvalidUsageOfThis
                                this.isSubmitting = false;
                            }
                        });
                    } else {
                        return false;
                    }
                });
            },
            handlePhoneInput() {
                this.$nextTick(() => {
                    this.$set(this.form, 'phone', this.formatPhoneNumber(this.form.phone, false));
                    this.$refs['form'].validateField('phone');
                });
            },
            handleSendConfirmation(type) {
                if (!this.form[type]) {
                    let errors = {};
                    errors[type] = [type + " is required"];
                    this.errors.record(errors);
                    this.$refs[type + '-input'].focus();
                } else {
                    if (this.codeSent) {
                        this.$confirm('Are you sure to send confirmation code again?', 'Warning', {
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel',
                            type: 'warning'
                        }).then(() => {
                            this.sendConfirmation(type);
                        });
                    } else {
                        this.sendConfirmation(type);
                    }
                }
            },
            sendConfirmation(type) {
                this[type + 'CodeSending'] = true;
                let data = {};
                data[type] = this.form[type];
                alert('Not implemented');
                return 0;
                /*sendConfirmationCode(data).then((response) => {
                    this.$message.success(response.data.message);
                    this[type + 'CodeSending'] = false;
                    this.codeSent = true;
                }).catch((error) => {
                    if (error.response.data) {
                        if (error.response.data.errors) {
                            this.errors.record(error.response.data.errors);
                        } else if (error.response.data.message) {
                            this.$message.error(error.response.data.message);
                        } else {
                            this.$message.error('Unknown server error');
                        }
                    }
                    this[type + 'CodeSending'] = false;
                });*/
            }
        }
    };
</script>
