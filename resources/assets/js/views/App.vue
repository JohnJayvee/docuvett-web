<template>
    <div id="app">
        <transition v-if="$auth.ready()" name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
        <div v-if="!$auth.ready()" class="loading-screen">
            <div class="loading-text">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</template>

<script>
    import methods from "~/includes/_common_/util";
    import selectEventListeners from '~/mixins/selectEventListeners';

    export default {
        name: 'App',
        components: {},
        mixins: [selectEventListeners],
        data() {
            return {};
        },
        computed: {
            modulesAvailable() {
                return this.$store.state.Modules.availableList;
            },
        },
        mounted() {
            if (window.navigator && !window.navigator.onLine) {
                this.$auth.logout({
                    makeRequest: true,
                    redirect: '/login'
                });
            }

            bus.$on('auth:refresh', this.refreshUser);
        },
        beforeDestroy() {
            bus.$off('auth:refresh', this.refreshUser);
        },
        methods: {
            ...methods,
            async refreshUser() {
                try {
                    await this.$auth.refresh();

                    if (window.sessionNotification) window.sessionNotification.close();
                } catch (e) {}
            },
        },
    };

</script>

<style lang="scss">
    @import "../../sass/element-ui-colors";
    @import "../../sass/element-ui-variables";

    #app {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
    }
</style>
