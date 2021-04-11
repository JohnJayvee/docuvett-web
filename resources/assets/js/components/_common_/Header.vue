<template>
    <header class="login-header">
        <div id="top-bar-menu" class="top-bar relative">
            <div class="top-bar-left pull-left relative">
                <a :href="wordpressURL" class="logo-link">
                    <div v-if="laravel.appBadge" class="badge">{{ laravel.appBadge }}</div>
                    <img class="logo show-for-large" src="../../../img/Logo_Light.png" :alt="laravel.appName">
                </a>
            </div>
            <div class="top-bar-right show-for-medium relative ">
                <ul class="clearfix">
                    <li>
                        <router-link v-if="$route.name !== 'Login'" :to="{name: 'Login'}" class="button secondary-button sign-up-button">
                            <i class="el-icon-fa-sign-in" aria-hidden="true"></i> Sign In
                        </router-link>
                        <router-link v-else-if="laravel.appEnable.registration" :to="{name: 'Sign Up'}" class="button secondary-button sign-up-button">
                            <i class="el-icon-fa-lock" aria-hidden="true"></i>  Sign Up
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</template>

<style lang="scss" scoped="">
    @import "../../../sass/element-ui-colors";
    @import "../../../sass/element-ui-variables";

    .relative {
        position: relative;
    }
    .float-left {
        float: left !important;
    }
    .clearfix:after {
        clear: both;
    }

    .login-header {
        position: absolute;
        width: 100%;
        &:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.80);
            z-index: 1;
        }
        .top-bar {
            margin: auto;
            padding: 20px;
            max-width: 1140px;
            background-color: transparent;
            z-index: 999;
            height: 98px;
            @media (max-width: $--xs - 1) {
                height: 65px;
                padding: 10px 20px;
            }
            .top-bar-left {
                height: 100%;
                .logo-link {
                    display: flex;
                    height: 100%;
                    align-items: center;
                    .badge {
                        text-transform: uppercase;
                        position: absolute;
                        color: $--color-primary;
                        font-weight: 700;
                        display: inline-block;
                        bottom: -2px;
                        right: -2px;
                        font-size: 15px;
                        @media (max-width: $--xs - 1) {
                            bottom: -3px;
                            right: -18px;
                            font-size: 14px;
                        }
                    }
                    .logo {
                        height: 60%;
                        object-fit: contain;
                    }
                }
            }
            .top-bar-right {
                float: right;
                font-size: 18px;
                font-weight: 400;
                height: 100%;
                display: table;
                .clearfix {
                    padding: 0;
                    margin: 0;
                    display: table-cell;
                    vertical-align: middle;
                    #menu-main-menu-1 {
                        padding: 0;
                        .menu-item {
                            margin-left: 0;
                            &:first-child {
                                padding-left: 0;
                            }
                            @media (max-width: $--md - 1) {
                                display: block;
                                line-height: 20px;
                                font-size: 16px;
                                padding-left: 0;
                            }
                        }
                    }
                }
                a {
                    color: $--color-text-primary;
                    text-decoration: none;
                }
                li {
                    padding-left: 0;
                    display: inline-block;
                    vertical-align: middle;
                    .menu {
                        display: inline-block;
                        float: right;
                        li {
                            padding-left: 33px;
                            margin-left: 3px;
                            display: inline-block;
                            a {
                                outline: 0;
                            }
                        }
                    }
                }
                .button {
                    font-size: 14px;
                    margin-left: 68px;
                    display: inline-block;
                    text-align: center;
                    line-height: 1;
                    cursor: pointer;
                    -webkit-appearance: none;
                    transition: background-color .25s ease-out, color .25s ease-out;
                    vertical-align: middle;
                    outline: 0;
                    padding: 10px 16px;
                    border-radius: 5px;
                    font-weight: 600;

                    @media (max-width: $--md - 1) {
                        margin-left: 12px;
                    }
                }
                .secondary-button {
                    background-color: transparent;
                    border: 1px solid $--color-primary;
                    //color: #FFF;
                    i {
                        margin-right: 5px;
                    }
                }
                .sign-up-button {
                    @media screen and (max-width: $--xs - 1) {
                        margin-left: 0;
                    }
                }
            }
        }
    }
</style>

<script>
    export default {
        data() {
            return {
                laravel: Laravel,
                wordpressURL: '#'
            };
        }
    };
</script>