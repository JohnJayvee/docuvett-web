<template>
    <common-dashboard v-if="!dashboardName"></common-dashboard>
</template>

<script>
    import Dashboard from './Dashboard/Dashboard';

    export default {
        name: 'Dashboard',
        components: {
            'common-dashboard': Dashboard,
        },
        computed: {
            _: _,
            dashboardName: () => {
                return _.head(Vue.$auth.user().roles).dashboard || null;
            }
        }
    };
</script>
