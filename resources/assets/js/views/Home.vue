<template>
    <el-row class="container">
        <el-header ref="header" :class="{'show-menu': phoneView && menuVisible}" class="header">
            <div v-if="laravel.appBadge" class="beta-ribbon">
                {{ laravel.appBadge }}
            </div>
            <img :class="collapsed?'logo-collapse-width':'logo-width'" class="logo-img hide-xxs-only" src="../../img/Logo_Dark.png">
            <div :class="collapsed?'tools-collapsed':''" class="tools hide-xs-only" @click.prevent="collapse">
                <i v-if="!(phoneView && menuVisible)" :class="phoneView?'el-icon-fa-align-justify':'el-icon-arrow-left'"></i>
                <i v-else class="el-icon-fa-times"></i>
            </div>

            <div class="user-info">
                <el-dropdown trigger="click" :show-timeout="0" :hide-timeout="0" class="pull-right">
                    <div class="el-dropdown-link user-info-inner">
                        <img v-if="!!$auth.user().avatar" :src="$auth.user().avatar">
                        <div v-else class="icon icon_unknown-user"></div>
                    </div>
                    <el-dropdown-menu slot="dropdown" class="top-menu-dropdown">
                        <router-link v-if="$auth.check('account-settings.index')" :to="{name: 'Account Settings'}">
                            <el-dropdown-item>Account Settings</el-dropdown-item>
                        </router-link>
                        <router-link v-if="$auth.check('tasks.index') && modulesAvailable['Tasks']" :to="{name: 'Tasks'}">
                            <el-dropdown-item>Tasks</el-dropdown-item>
                        </router-link>
                        <el-dropdown-item v-if="$auth.impersonating()" @click.native="unimpersonate">
                            Logout from "{{ $auth.user().name }}"
                        </el-dropdown-item>
                        <el-dropdown-item @click.native="logout">
                            Exit
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>

            <!--mobile menu-->
            <el-menu :default-active="$route.path" class="mobile-menu" router unique-opened @select="handleMenuSelect">
                <template v-for="routeItem in $router.options.routes" v-if="routeItem.menu">
                    <template v-for="(item,index) in routeItem.children" v-if="menuItemIsAvailable(item)">
                        <el-submenu v-if="item.children && item.children.length" :key="index" :index="String(index)">
                            <template slot="title">
                                <i :class="item.iconCls"></i>
                                <span slot="title">{{ getMenuItemName(item) }}</span>
                            </template>
                            <el-menu-item
                                v-for="child in item.children"
                                v-if="menuItemIsAvailable(child)"
                                :key="child.name"
                                :class="[childComponentClass(child), getSelectedMenuItemClass(child)]"
                                :index="child.path">
                                <i :class="child.iconCls"></i>
                                <span slot="title">{{ getMenuItemName(child) }}</span>
                            </el-menu-item>
                        </el-submenu>
                        <el-menu-item
                            v-else-if="menuItemIsAvailable(item)"
                            :key="item.name"
                            :class="(item.component ? '' : 'is-disabled')"
                            :index="item.path">
                            <i :class="item.iconCls"></i>
                            <span slot="title">{{ getMenuItemName(item) }}</span>
                        </el-menu-item>
                    </template>
                </template>
            </el-menu>
        </el-header>
        <el-col :span="24" class="main">
            <!--regular menu-->
            <el-menu v-if="!phoneView" :collapse="collapsed" :default-active="$route.path" class="el-menu-vertical" router unique-opened>
                <template v-for="routeItem in $router.options.routes" v-if="routeItem.menu">
                    <template v-for="(item,index) in routeItem.children" v-if="menuItemIsAvailable(item)">
                        <el-submenu v-if="item.children && item.children.length && subMenuItemIsAvailable(item)" :key="index" :index="String(index)">
                            <template slot="title">
                                <i :class="item.iconCls"></i>
                                <span slot="title">{{ getMenuItemName(item) }}</span>
                            </template>
                            <el-menu-item
                                v-for="child in item.children"
                                v-if="menuItemIsAvailable(child)"
                                :key="child.name"
                                :class="[childComponentClass(child), getSelectedMenuItemClass(child)]"
                                :index="child.path"
                                :route="child">
                                <i :class="child.iconCls"></i>
                                <span slot="title">{{ getMenuItemName(child) }}</span>
                            </el-menu-item>
                        </el-submenu>
                        <el-menu-item
                            v-else-if="menuItemIsAvailable(item) && subMenuItemIsAvailable(item)"
                            :key="item.name"
                            :class="[(item.component ? '' : 'is-disabled'), getSelectedMenuItemClass(item)]"
                            :index="item.path"
                            :route="item">
                            <i :class="item.iconCls"></i>
                            <span slot="title">{{ getMenuItemName(item) }}</span>
                        </el-menu-item>
                    </template>
                </template>
            </el-menu>

            <div id="scrollable-container" class="content-container">
                <el-col :span="24" class="content-wrapper">
                    <transition mode="out-in" name="fade">
                        <router-view :key="$route.fullPath"></router-view>
                    </transition>
                </el-col>
            </div>
        </el-col>
    </el-row>
</template>

<style lang="scss">
@import "~@/element-ui-colors";
@import "~@/element-ui-variables";

$menu-width: 230;
.no-pointer-events {
    pointer-events: none;
}

.icon_unknown-user {
    width: 50px;
    height: 50px;
}

.company-byline {
    font-size: 15px;
    line-height: 24px;
}

.invisible {
    display: none;
}

.beta-ribbon {
    text-transform: uppercase;
    pointer-events: none;
    font-size: 11px;
    font-weight: bold;
    width: 133px;
    background: $--color-danger;
    position: absolute;
    top: 6px;
    left: -50px;
    text-align: center;
    line-height: 19px;
    letter-spacing: 1px;
    color: $--color-white;
    transform: rotate(-45deg);
    -webkit-transform: rotate(-45deg);
    box-shadow: 0 3px 15px -3px black;
}

.el-menu-vertical:not(.el-menu--collapse) {
    width: #{$menu-width}px;
}

.el-menu-vertical {
    overflow: auto;
    background-color: $--color-primary;
    border-right: none;
    @media screen and (max-width: $--xs - 1) {
        display: none !important;
    }
    @media (min-width: $--xs) and (max-width: $--sm - 1) {
        transition: unset !important;
    }

    &::-webkit-scrollbar {
        width: 0;
    }

    .el-menu {
        background-color: $--color-primary;
    }

    .el-submenu {
        .el-submenu__title {
            font-weight: 500;
            background: transparent !important;
            color: $--color-white;

            i {
                color: $--color-white;
            }

            .el-submenu__icon-arrow {
                color: $--color-white;
                font-weight: bold;
                font-size: 14px;
            }
        }

        &.is-active {
            color: $--color-white;
            background-color: $--color-primary-dark;
        }

        &:hover {
            color: $--color-white;
            background-color: $--color-hover-bg;
        }

        &.is-disabled {
            opacity: 0.3;
            pointer-events: none;
        }

        .el-menu-item {
            min-width: 100%;
        }
    }

    .el-menu-item {
        color: $--color-white;
        font-weight: 500;

        &.is-active {
            color: $--color-white;
            background-color: $--color-item-bg;
        }

        &:hover {
            color: $--color-white;
            background-color: $--color-primary-dark;
        }

        &:focus:not(.is-active) {
            outline: none;
            background-color: $--color-primary;
        }

        i {
            color: $--color-white;
        }

        &.is-disabled {
            opacity: 0.3;
            pointer-events: none;
        }
    }
}

.el-menu--popup {
    .el-menu-item {
        i {
            color: $--color-text-primary;
        }

        &.is-active {
            background-color: $--color-primary-light-9;
        }
    }
}

.horizontal-collapse-transition {
    .el-badge {
        display: none;
    }
}

.el-menu {
    .el-badge__content {
        margin-bottom: 3px;
        border: none;
    }
}

.show-menu {
    height: 100% !important;
    overflow-y: auto !important;
}

.mobile-menu {
    background: transparent;
    box-shadow: none;
    overflow-x: auto;
    border: none;
    width: 100%;

    .el-menu {
        background: transparent;
        box-shadow: none;
    }

    .el-menu-item, .el-submenu__title {
        cursor: pointer;
        color: $--color-text-regular;

        i {
            color: $--color-text-regular;
        }

        &:hover {
            background: $--color-primary-light-9;
        }

        &.is-active {
            color: $--color-primary;

            i {
                color: $--color-primary;
            }
        }
    }

    .el-submenu .el-menu {
        background: rgba(180, 180, 180, 0.1) !important;
    }

    .el-submenu.is-opened {
        .el-submenu__title {
            box-shadow: 0 4px 10px -3px #888888;
        }

        & + .el-submenu, & + .el-menu-item {
            box-shadow: 0 -4px 10px -3px #888888;
        }
    }
}

h2.page-title {
    font-weight: normal;
    color: $--color-text-regular;
    margin: 15px 0;
}

.search-results {
    div {
        display: flex;
        justify-content: space-between;
        color: $--color-black;
        text-decoration: none;
        font-size: 13px;
        overflow: hidden;

        .result {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .field {
            color: $--color-primary;
            text-transform: capitalize;
        }

        .value {
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 5px;
        }

        .highlight {
            background: $--color-warning-light;
        }

        .type {
            font-size: 11px;
            color: $--color-info;
        }
    }

    .no-results {
        flex-direction: column;
        align-items: center;
        color: $--color-info;

        .chars {
            display: block;
            color: $--color-warning;
        }
    }
}

.container {
    /*position: absolute;*/
    /*top: 0px;*/
    /*bottom: 0px;*/
    width: 100%;
    height: 100%;

    .header {
        top: 0;
        left: 0;
        position: fixed;
        width: 100%;
        overflow: hidden;
        transition: height 0.2s;
        z-index: 1999;
        height: 60px;
        line-height: 60px;
        background-color: $--color-white;
        background-size: cover;
        color: $--color-text-secondary;
        padding: 0;
        box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.4);

        .title {
            font-size: $--font-size-large;
        }

        @media (max-width: $--xs - 1) {
            background-size: initial;
            background-position-x: 15%;
        }
        @media (min-width: $--xs) and (max-width: $--sm - 1) {
            .title {
                padding-left: 20px;
            }
        }

        .help-icon {
            float: right;
            padding-right: 20px;
            height: 60px;

            i {
                line-height: 60px;
                color: $--color-text-secondary;
                font-size: 22.5pt;
            }
        }

        .global-search {
            text-align: right;
            float: right;
            padding-right: 30px;
            margin-right: 20px;

            .el-input {
                width: 300px;
            }

            border-right: $--border-base;

            @media screen and (max-width: $--xs - 1) {
                margin: 0;
                padding: 0 20px;
                border: none;
                & .el-input {
                    width: calc(100vw - 40px);
                }
            }
        }

        .user-info {
            text-align: right;
            padding-right: 20px;
            float: right;

            .header-notification {
                .el-loading-mask {

                    .el-loading-spinner {
                        margin-top: -28px;

                        .circular {
                            height: 20px;
                            width: 20px;
                        }
                    }
                }

                .user-notification {
                    cursor: pointer;

                    i {
                        color: $--color-text-secondary;
                    }
                }
            }

            .user-info-inner {
                cursor: pointer;
                color: $--color-text-secondary;
                display: flex;
                height: 100%;

                img {
                    width: 40px;
                    height: 40px;
                    border-radius: 20px;
                    margin: 10px 0 10px 10px;
                    float: right;
                    object-fit: cover;
                }

                &:after {
                    content: '';
                    background-color: $--color-success;
                    width: 10px;
                    height: 10px;
                    display: inline-block;
                    position: relative;
                    top: 40px;
                    right: 10px;
                    border-radius: 50%;
                }
            }

            .user-action-icon {
                margin-right: 20px;
                vertical-align: unset;

                sup {
                    top: 20px;
                    padding: 0 3px;
                    min-width: 12px;
                }
            }
        }

        .logo-img {
            height: 100%;
            padding: 10px 0;
            width: #{$menu-width}px;
            object-fit: contain;
            box-sizing: border-box;
            transition: 0.3s width ease-in-out, 0.3s padding-left ease-in-out, 0.3s padding-right ease-in-out;
            background-color: $--color-primary;
            float: left;

            &.logo-collapse-width {
                width: 64px;
            }

            @media (min-width: $--xs) and (max-width: $--sm - 1) {
                transition: unset !important;
            }
        }

        .tools {
            width: 34px;
            height: 34px;
            line-height: 34px;
            cursor: pointer;
            display: inline-block;
            float: left;

            i {
                font-weight: bold;
                transition: 0.3s all ease-in-out;
            }

            @media screen and (min-width: $--xs) {
                background: $--color-danger;
                border-radius: 50%;
                color: $--color-white;
                text-align: center;
                margin-left: -17px;
                margin-top: 13px;
                box-shadow: 0 0 3px rgba(0, 0, 0, 0.5);

                &.tools-collapsed {
                    i {
                        transform: rotate(180deg);
                    }
                }
            }
            @media screen and (max-width: $--xs - 1) {
                height: 60px;
                line-height: 60px;
                padding: 0 20px;
                width: 24px;
                text-align: center;
            }

            .el-icon-fa-times {
                font-size: $--font-size-large;
            }
        }
    }

    .main {
        display: flex;
        position: absolute;
        top: 60px;
        bottom: 0;
        overflow: hidden;

        .content-container {
            padding: 20px;
            flex: 1;
            border: none;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;

            .content-wrapper {
                box-sizing: border-box;
            }

            @media screen and (max-width: $--xs - 1) {
                padding: 5px;
            }
        }
    }
}

.el-dropdown-menu {
    a {
        color: inherit;
        text-decoration: inherit;
    }
}

.top-menu-dropdown {
    max-width: 350px;
    width: calc(100% - 12px);

    li {
        overflow: hidden;

        &.active {
            background-color: $--color-primary-light-9;
            color: $--color-primary-light-2;
        }
    }

    .active-company-logo {
        border-radius: 50%;
        float: left;
        width: 70px;
        height: 70px;
        object-fit: cover;
        padding: 10px;
        margin-left: -10px;

        .empty-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            background: $--color-white;
            color: $--color-text-placeholder;
            border: $--border-base;
            border-radius: $--border-radius-base;
            box-sizing: border-box;
            font-size: 25px;

            i {
                margin-right: 0;
            }
        }
    }

    .company-name.active, .company-byline {
        margin-left: 80px;
    }
}

.system-message-icon {
    font-size: 25px;
    margin-top: 7px;
}

.top-menu-dropdown {
    .el-row-notification {
        border-bottom: 1px solid $--border-color-lighter;
    }
}

.el-row-empty {
    margin: auto;
    width: 50px;
    color: $--color-text-placeholder;
}

.user-name-home:hover, .company-home:hover {
    color: #3678a8 !important;
}

.user-name-home, .company-home {
    color: #00395a !important;
}

.notification-off > .el-badge__content {
    background: $--color-text-secondary;
}
</style>

<script>
    import axios from 'axios';
    import {computed, filters, methods} from '~/includes/_common_/util';

    const CancelToken = axios.CancelToken;

    export default {
        filters: {
            ...filters,
        },
        components: {},
        data() {
            return {
                laravel: Laravel,
                collapsed: false,
                menuVisible: false,
                phoneView: false,
                timeout: null,
            };
        },
        computed: {
            ...computed,
            _: _,
            formattedNotifications() {
                const today = this.formatDateToHumanDate(new Date());
                const yesterday = this.formatDateToHumanDate(new Date((new Date()).setDate((new Date()).getDate() - 1)));
                const notifications = this.systemMessages.map(message => {
                    const date = this.formatDateToHumanDate(message.updated_at);

                    message.type = message.type.replace('-permanent', '');
                    message.date = date === today
                        ? 'Today'
                        : date === yesterday ? 'Yesterday' : date;

                    return message;
                });
                return _.groupBy(notifications, 'date');
            },
        },
        watch: {
            menuVisible: function (value) {
                if (this.phoneView) {
                    let app = document.getElementById('app');
                    if (value) {
                        app.classList.add('menu-opened');
                    } else {
                        app.classList.remove('menu-opened');
                    }
                }
            },
            phoneView: function () {
                this.$refs.header.$el.scrollTop = 0;
            },
        },
        mounted() {},
        methods: {
            ...methods,
            childComponentClass(child) {
                let newClass = '';
                newClass = child.component ? '' : 'is-disabled';
                newClass += (child.name === 'Queues' || child.name === 'Team Runs' ? ' invisible' : '');
                return newClass;
            },
            menuItemIsAvailable() {
                return true;
            },
            subMenuItemIsAvailable(rootItem) {
                let allowed;
                if (rootItem.children && rootItem.children.length) {
                    allowed = _.some(rootItem.children, item => this.menuItemIsAvailable(item) && !!item.component);
                } else {
                    allowed = true;
                }
                return allowed;
            },
            logout: function () {
                this.$confirm('Are you sure you want to exit?', 'Confirmation', {
                    type: 'info'
                }).then(() => {
                    this.$notify.closeAll();
                    this.$auth.logout({
                        makeRequest: true,
                        redirect: '/login'
                    });
                }).catch(() => {
                });
            },
            prepareUserData(companyID) {
                let formData = new FormData();
                formData.append('id', this.$auth.user().id);
                formData.append('active_company', companyID);
                formData.set('_method', 'PUT');
                return formData;
            },
            collapse: function () {
                if (this.phoneView) {
                    this.menuVisible = !this.menuVisible;
                    this.$refs.header.$el.scrollTop = 0;
                } else {
                    this.collapsed = !this.collapsed;
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 350);
                }
            },
            getWindowWidth: _.debounce(function () {
                if (document.documentElement.clientWidth < 768) {
                    this.collapsed = true;
                }
                this.phoneView = document.documentElement.clientWidth < 480;
            }, 100, {
                maxWait: 100
            }),
            handleMenuSelect() {
                this.menuVisible = false;
                this.$refs.header.$el.scrollTop = 0;
            },
            getMenuItemName(item) {
                return item.meta && item.meta.menuName ? item.meta.menuName : item.name;
            },
            getSelectedMenuItemClass(item) {
                return item.name === this.$route.name ? 'no-pointer-events' : '';
            },
        },
    };

</script>
