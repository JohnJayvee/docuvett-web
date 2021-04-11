<template>
    <section>
        <el-row class="page-header" justify="space-between" type="flex">
            <el-col :span="23">
                <h2 class="page-title">
                    Dashboard
                </h2>
            </el-col>
            <!--            <el-col :span="1">
                <el-tooltip class="item" content="Hidden widgets" effect="dark" placement="top-start">
                    <el-dropdown v-if="!!hiddenWidgets.length" class="dropdown-hidden-widgets">
                        <span class="el-dropdown-link"><i class="el-icon-copy-document"></i></span>
                        <el-dropdown-menu v-if="!!hiddenWidgets.length" slot="dropdown">
                            <el-dropdown-item
                                v-for="(hiddenWidget, key) in hiddenWidgets"
                                :key="key"
                                @click.native="makeVisible(hiddenWidget)">
                                {{ hiddenWidget.widget_header }}
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-tooltip>
            </el-col>-->
        </el-row>
        <!--<card-details v-if="!!$route.params.checkSubscriptionTrial"></card-details>-->

        <!--        <el-row id="staticWidgets" style="padding-right: 10px">
            <el-col v-for="(widget, key) in staticWidgets" :key="key" :lg="6" :md="12" :sm="24" :xl="6" :xs="24" class="widget-col">
                <el-card class="box-card">
                    <component :is="widget.component" :item="widget"></component>
                </el-card>
            </el-col>
        </el-row>

        <grid-layout
            :breakpoints="{lg: 1200, md: 996, sm: 768, xs: 480, xxs: 0 }"
            :col-num="4"
            :cols="{ lg: 4, md: 4, sm:1, xs: 1, xxs: 1 }"
            :is-draggable="true"
            :is-resizable="true"
            :layout.sync="visibleWidgets"
            :margin="[10, 10]"
            :responsive="true"
            :row-height="30"
            :vertical-compact="true"
            @layout-updated="onLayoutUpdated">
            <grid-item
                v-for="item in visibleWidgets"
                :key="item.i"
                :h="item.h"
                :i="item.i"
                :is-draggable="true"
                :is-resizable="true"
                :static="(!!item.is_static)"
                :w="item.w"
                :x="item.x"
                :y="item.y">
                <el-card class="box-card">
                    <div slot="header" class="clearfix widget-header">
                        <el-dropdown placement="bottom-start">
                            <span class="widget-title">{{ item.widget_header }}</span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item @click.native="handleDropdownClick('hide', item)">
                                    hide
                                </el-dropdown-item>
                                <el-dropdown-item @click.native="handleDropdownClick('delete', item)">
                                    delete
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>
                    <component :is="item.widget_name" :is-widget="true" :item="item"></component>
                </el-card>
            </grid-item>
        </grid-layout>-->
    </section>
</template>

<style lang="scss" scoped>

@import "~@/element-ui-colors";
@import "~@/element-ui-variables";

.page-header {
    padding: 0 10px;
}

/deep/ .el-card {
    margin-bottom: 0;
    height: 100%;
    overflow: auto;
}

/deep/ .el-card__header {
    padding-bottom: 0;
}

/deep/ .el-icon-copy-document {
    font-size: 30px;
    color: $--color-text-secondary;
    cursor: pointer;
}

.dropdown-hidden-widgets {
    margin-top: 15px;
    display: inline-block;
    float: right;
}

.widget-title {
    font-size: 12px;
    font-weight: normal;
    color: $--color-text-regular;
    cursor: pointer;
}

.widget-header {
    display: flex;
    justify-content: space-between;
}

.widget-settings {
    display: inline-block;
}

#staticWidgets {
    .box-card {
        margin: 0 0 0 10px
    }

    .widget-col {
        margin-bottom: .8rem;
    }
}

@media screen and (max-width: $--lg - 1) {
    #staticWidgets {
        .box-card {
            margin: 10px 0 0 10px
        }
    }
}
</style>

<script>
    import {filters, methods} from '~/includes/_common_/util';

    export default {
        filters,
        components: {},
        data() {
            return {
                listLoading: false,
                visibleWidgets: [],
                hiddenWidgets: [],
                staticWidgets: [],
            };
        },
        computed: {
            _: () => _,
        },
        methods: {
            ...methods,
            handleDropdownClick(action) {
                if (action === 'hide') {
                    this.listLoading = true;
                }
                if (action === 'static') {

                }
                if (action === 'delete') {
                    this.$confirm('This will permanently delete the widget. Continue?', 'Warning', {
                        type: 'warning'
                    }).then(() => {
                        this.listLoading = true;

                    });
                }
            },
            onLayoutUpdated(e) {
                let items = [];
                _.each(e, o => {
                    items.push({i: o.i, x: o.x, y: o.y, w: o.w, h: o.h});
                });
            },
            makeVisible() {
                this.listLoading = true;
            },
            setWidgets(data) {
                this.visibleWidgets = [
                    ...data.visibleWidgets
                ];
                this.hiddenWidgets = [
                    ...data.hiddenWidgets
                ];
                this.staticWidgets = [
                    ...data.staticWidgets
                ];
            },
        },
    };
</script>
