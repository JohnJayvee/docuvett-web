/* Common pages */
import Home from '../views/Home.vue';
import NotFound from '../views/_common_/404.vue';
import Login from '../views/_common_/Login.vue';
import SignUp from '../views/_common_/SignUp.vue';
import Reset from '../views/_common_/Reset.vue';
import Response from '../views/_common_/Response.vue';
import Dashboard from '../views/_common_/Dashboard.vue';

/* Admin section */
import Audits from '../views/Admin/setup/Audits.vue';
import Users from '../views/Admin/setup/Users.vue';
import Roles from '../views/Admin/setup/Roles.vue';

/* Customer */


/* Defining routes */
import SubMenu from '../components/_common_/SubMenu.vue';
import UIStyleGuide from '../components/_common_/UIStyleGuide';

let dashboard = () => import(/* webpackChunkName: "dashboard" */ '../views/_common_/Dashboard.vue');

let IfAuthRedirectToDashboard = (to, from, next) => {
    if (window.Vue && window.Vue.$auth.check()) {
        next({name: 'Dashboard'});
    } else {
        next();
    }
};

let IfRegistrationEnabled = (to, from, next) => {
    if (window.Vue && window.Vue.$auth.check()) {
        next({name: 'Dashboard'});
    } else if (!window.Laravel.appEnable.registration) {
        next({name: 'Login'});
    } else {
        next();
    }
};

let home = {
    name: 'Dashboard',
    title: 'Home',
};

const breadcrumb = (name, title, name2, title2) => {
    let bc = [home, {name: name, title: title ? title : name}];
    if (name2 || title2) bc.push({name: name2, title: title2 ? title2 : name2});
    return {breadcrumb: bc};
};

let routes = [
    {
        path: '',
        component: Home,
        meta: {auth: true},
        menu: true,
        children: [
            {path: '/', component: () => dashboard(), name: 'Dashboard', iconCls: 'el-icon-fa-th-large'},
            {
                path: '/',
                component: SubMenu,
                name: 'Admin',
                iconCls: 'el-icon-fa-universal-access ',
                meta: {auth: ['roles.index', 'users.index', 'audits.index',]},
                children: [
                    {path: '/roles', component: Roles, name: 'Roles', iconCls: 'el-icon-fa-suitcase', meta: {auth: ['roles.index'], ...breadcrumb('Roles', 'Roles & Permissions')}},
                    {path: '/users', component: Users, name: 'Users', iconCls: 'el-icon-fa-users', meta: {auth: ['users.index'], ...breadcrumb('Users')}},
                    {path: '/audits', component: Audits, name: 'Audits', iconCls: 'el-icon-fa-archive', meta: {auth: ['audits.index'], ...breadcrumb('Audits')}},
                ]
            },
        ]
    },
    {path: '/login', component: Login, name: 'Login', hidden: true, beforeEnter: IfAuthRedirectToDashboard},
    {path: '/sign-up', component: SignUp, name: 'Sign Up', hidden: true, beforeEnter: IfRegistrationEnabled},
    {path: '/password-reset', component: Reset, name: 'Reset', hidden: true, beforeEnter: IfAuthRedirectToDashboard},
    {path: '/response', component: Response, name: 'Response', hidden: true},
    {path: '/404', component: NotFound, hidden: true},
    {path: '*', component: NotFound, hidden: true},
    {path: '/ui-style-guide', component: UIStyleGuide, name: 'UI Style Guide', hidden: true, meta: {...breadcrumb('Style Guide')}},
];

export default routes;
