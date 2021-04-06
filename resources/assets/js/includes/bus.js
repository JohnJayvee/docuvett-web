import Echo from "laravel-echo";
import util from "./_common_/util";
import push from "./push";

// const messaging = firebase.messaging();

export default {
    data: {
        listenChannelName: '',
    },
    created: function () {
        this.$on('userLoggedIn', function (data) {
            setTimeout(() => {
                this.startWebSocketListeners(data);
            }, 300);
            if (push) push.requestPermission();
        });

        this.$on('userLoggedOff', function () {
            this.stopWebSocketListeners();
        });

        this.$on('notification.CommonUserNotification', function (data) {
            const $message = this.$message[data.status];
            if ($message) {
                $message(data.message);
            }
        });

        this.$on('notification.SubscriptionNotification', function (data) {
            Vue.$emit('module.Subscribe', data);
        });

        this.$on('notification.ModuleStatusNotification', function (data) {
            Vue.$emit('module.StatusChange', data);
        });

        this.$on('AppointmentDataUpdated', function (data) {
            Vue.$emit('appointment.updated', data);
        });

        this.$on('ContactDataUpdated', function (data) {
            Vue.$emit('contact.updated', data);
        });

        this.$on('TaskDataUpdated', function (data) {
            Vue.$emit('task.updated', data);
        });

        this.$on('SiteEventDataUpdated', function (data) {
            Vue.$emit('siteEvent.updated', data);
        });

        this.$on('OrderComplete', function (data) {
            Vue.$emit('order.complete', data);
        });

        this.$on('SystemMessageUpdated', function (data) {
            Vue.$emit('systemMessage.updated', data);
        });

        this.$on('InboxUpdated', function (data) {
            Vue.$emit('inbox.updated', data);
        });

        this.$on('TerminologyManagerUpdated', function (data) {
            console.log('TerminologyManagerUpdated');

            Vue.$emit('terminologyManager.updated', data);
        });

        this.$on('notification.EventReminder', function (e) {
            if (e && e.data && e.data.title) {
                this.$notify.info({
                    title: 'Reminder: ' + e.data.title,
                    message: '<div class="message-notification">' + e.data.description + '</div>',
                    dangerouslyUseHTMLString: true,
                    duration: 0,
                });
            }
        });

        this.$on('SystemMessageReceived', function (e) {
            if (e && e.data && e.data.message) {
                setTimeout(() => {
                    this.$notify({
                        duration: e.data.type === 'success-permanent' ? 0 : 5000,
                        title: 'New System Message',
                        message: '<div class="message-notification">' + e.data.message + '</div>',
                        type: e.data.type,
                        dangerouslyUseHTMLString: true
                    });
                }, 700);
            }
        });

        this.$on('notification.MessageReceived', function (e) {
            if (e && e.data && e.data.message) {
                let type = 'success';
                let title = 'New message received!';
                if (e.data.status === Laravel.messageStatus.rejected) {
                    type = 'warning';
                    title = 'Message was rejected!';
                } else {
                    if (e.data.moderated_at) {
                        type = 'success';
                        title = 'Message was approved!';
                    }
                    util.changeUserMessageCount('++');
                }
                this.$notify({
                    type: type,
                    title: title,
                    message: '<div class="message-notification">' + e.data.message + '</div>',
                    dangerouslyUseHTMLString: true,
                });
            }
        });

        this.$on('notification.UserRelatedSettingIsUpdated', function (response) {
            if (response.data.suspended === 1) {
                this.$message.error('Your account has been suspended');
                this.$auth.logout({
                    makeRequest: true,
                    redirect: '/login'
                });
            } else {
                this.$auth.user(response.data);
            }
        });

        this.$on('notification.AppointmentConfirmed', function (e) {
            if (e && e.message) {
                this.$notify({
                    duration: 50000,
                    title: 'New System Message',
                    message: '<div class="message-notification">' + e.message + '</div>',
                    type: e.status,
                    dangerouslyUseHTMLString: true
                });
            }
        });
    },

    methods: {
        authStateIsChanged: function (val) {
            if (!val) bus.$emit('userLoggedOff');
        },

        availableForUser: function (e) {
            return e.data.role_id === null || e.data.role_id === this.$auth.user().roles[0].id;
        },

        userChanged: function (val) {
            bus.$emit('userChanged');
        },

        startWebSocketListeners: function (user_data) {
            this.stopWebSocketListeners();
            try {
                window.Echo = window.io ? new Echo({
                    broadcaster: 'socket.io',
                    key: Laravel.pusher.key,
                    host: Laravel.pusher.scheme + '://' + window.location.hostname + ':' + Laravel.pusher.port,
                    auth: {
                        headers: {'Authorization': 'Bearer ' + Vue.$auth.token()}
                    },
                    encrypted: Laravel.pusher.encrypted
                    // authEndpoint: '/api/broadcasting/auth',
                }) : null;
            }
            catch (err) {
                window.Echo = null;
            }

            if (window.Echo && user_data) {
                this.listenChannelName = 'communication-channel.' + user_data.id;
                window.Echo.private(this.listenChannelName && !!this.$auth.user().notification_status)
                    .listen('EventReminder', (e) => {
                        e && bus.$emit('notification.EventReminder', e);
                    })
                    .listen('MessageReceived', (e) => {
                        e && bus.$emit('notification.MessageReceived', e);
                    })
                    .listen('CommonUserNotification', (e) => {
                        e && bus.$emit('notification.CommonUserNotification', e);
                    })
                    .listen('SubscriptionNotification', (e) => {
                        e && bus.$emit('notification.SubscriptionNotification', e);
                    })
                    .listen('UserRelatedSettingIsUpdated', (e) => {
                        e && bus.$emit('notification.UserRelatedSettingIsUpdated', e);
                    })
                    .listen('AppointmentConfirmed', (e) => {
                        e && bus.$emit('notification.AppointmentConfirmed', e);
                    });

                window.Echo.channel('system-channel')
                    .listen('SystemMessageReceived', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('SystemMessageReceived', e);
                        }
                    })
                    .listen('ModuleStatusNotification', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('notification.ModuleStatusNotification', e);
                        }
                    })
                    .listen('AppointmentDataUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('AppointmentDataUpdated', e);
                        }
                    })
                    .listen('ContactDataUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('ContactDataUpdated', e);
                        }
                    })
                    .listen('TaskDataUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('TaskDataUpdated', e);
                        }
                    })
                    .listen('SiteEventDataUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('SiteEventDataUpdated', e);
                        }
                    })
                    .listen('OrderComplete', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('OrderComplete', e);
                        }
                    })
                    .listen('SystemMessageUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('SystemMessageUpdated', e);
                        }
                    }).listen('InboxUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('InboxUpdated', e);
                        }
                    })
                    .listen('TerminologyManagerUpdated', (e) => {
                        if (this.$auth.check('system-messages.list') && this.availableForUser(e)) {
                            e && bus.$emit('TerminologyManagerUpdated', e);
                        }
                    });
            }
        },

        stopWebSocketListeners: function () {
            if (window.Echo) {
                window.Echo.leave(this.listenChannelName);
                window.Echo.disconnect();
                delete window.Echo;
            }
            window.Echo = null;
            this.listenChannelName = '';
        },
    }
};