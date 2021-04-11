<template>
    <el-container class="login-screen background-image">
        <app-header></app-header>
        <el-main class="login-section">
            <div class="v-align-container">
                <div class="t-cell">
                    <div class="login-container">
                        <div class="title">
                            {{ message }}
                        </div>
                    </div>
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
                /*margin-bottom: 18px;*/
                margin-top: 0;
                font-size: 22px;
                text-align: center;
                color: $--color-text-primary;
            }
        }

    }
</style>

<script>
    import {methods} from '../../includes/_common_/util';
    import appHeader from '../../components/_common_/Header';

    export default {
        components: {
            'app-header': appHeader,
        },
        data() {
            return {
                message: '',
            };
        },
        methods: {
            ...methods,
        },
        mounted() {
            this.message = this.getQueryStringByName('message') || 'No response message';
        }
    };
</script>