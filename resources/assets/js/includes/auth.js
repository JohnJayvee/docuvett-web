import {Ziggy} from './ziggy';

window.Ziggy  = Ziggy;
window.zRoute = require('../../../../vendor/tightenco/ziggy/dist/js/route');

export default {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    rolesVar:           'permissionList',
    loginData:          { url: zRoute('api.login').url() },
    logoutData:         { url: zRoute('api.logout').url() },
    fetchData:          { url: zRoute('users.current').url() },
    registerData:       { url: zRoute('api.register').url() },
    refreshData:        { url: zRoute('api.refresh').url(), interval: 0 },
    impersonateData:    { url: zRoute('users.login-as').url() },
    parseUserData: function (data) {
        bus.$emit('userLoggedIn', data);
        return data;
    },
};
