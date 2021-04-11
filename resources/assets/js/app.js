/**
 * JavaScript dependencies
 */

window._ = require('lodash');
window.Axios = require('axios');

/**
 * Ajax config
 */

Axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

/**
 * Import Vendor dependencies
 */
import Vue                from 'vue';
import VueRouter          from 'vue-router';
import VueAxios           from 'vue-axios';
import VueAuth            from '@websanova/vue-auth';
import store              from './store/store';
import ElementUI          from 'element-ui';
import lang               from 'element-ui/lib/locale/lang/en';
import locale             from 'element-ui/lib/locale';
import moment             from 'moment';
import momentTimezone     from 'moment-timezone';
import VueTelInput        from 'vue-tel-input';

Vue.use(VueTelInput);

/**
 * Import Application includes
 */
import routes             from './includes/routes';
import authParams         from './includes/auth';
import bus                from './includes/bus';
import app_data           from './includes/app_data';
import app                from './views/App';
import methods            from "./includes/_common_/util";
import Errors             from './plugins/Errors';
locale.use(lang);

Vue.mixin({
    data() {
        return {};
    },
    methods: {
        zRoute: zRoute,
        GlobalFormatDate: function (data) {
            let _this = methods, global = window.Laravel.formatDate.global, format = _this.getFormat(data);
            let formats = [
                'yyyy-mm-dd',
                'yyyy-mm-dd hh:mm:ss'
            ];
            try{ // this is must because have error .toLowerCase undefined
                return _this.formatDateToDayDate(data, format.toLowerCase(), formats, global);
            }catch (e) {
                return _this.formatDateToDayDate(data, format, formats, global);
            }
        },
        GlobalFormatTime: function (time) {
            let _this = methods,
                globalTime = window.Laravel.formatDate.globalTime;
            return _this.formatTime(time, globalTime);
        }

    }
});

Vue.use(ElementUI);
Vue.use(VueRouter);
Vue.use(VueAxios, Axios);
Vue.use(Errors);

/* Attach Router */
Vue.router = new VueRouter({
    routes,
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        return new Promise((resolve) => {
            if (to.hash) {
                setTimeout(() => {
                    Vue.prototype.$nextTick(el => {
                        // noinspection JSAssignmentUsedAsCondition
                        if (el = document.querySelector(to.hash)) {
                            const position = el.getBoundingClientRect();
                            document.getElementById('scrollable-container').scrollTo(position.left, position.top);
                        }
                    });
                }, 100);
            } else if (savedPosition) {
                resolve(savedPosition);
            } else {
                resolve({x: 0, y: 0});
            }
        });
    }
});

/* Attach Authorization */
Vue.use(VueAuth, authParams);


/* Initialize Global Event Bus */
window.bus = new Vue(bus);


/* Initialize Vue App */
app.router = Vue.router;
app.router.beforeEach((to, from, next) => {
    const appName = window.Laravel.appName;
    let toTitle = appName;
    if (to.hasOwnProperty('meta') && to.meta.hasOwnProperty('title')) {
        toTitle = [to.meta.title, appName].join(' - ');
    }
    document.title = toTitle;
    if (!to.meta.module) {
        next();
        return;
    }
    methods.waitWhile("_.isEmpty(window.Vue.$store.state.Modules.availableList)", () => {
        if (store.state.Modules.availableList[to.meta.module]) {
            next();
            return;
        }
        next({name: methods.findNextAvailableRouteName(to.name, Vue.router.options.routes)});
    });
});
window.Vue = new Vue({
    store,
    render: h => h(app)
}).$mount('#app');

window.app_data = app_data;
window.Vue.$auth.watch.$watch('authenticated', bus.methods.authStateIsChanged);
window.Vue.$auth.watch.$watch('data.id', bus.methods.userChanged);
moment.locale('en-au');
window.moment = moment;
window.momentTimezone = momentTimezone;
