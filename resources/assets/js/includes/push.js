import axios from 'axios';
import firebase from 'firebase/app';
import 'firebase/firebase-messaging';

let config = {
    messagingSenderId: Laravel.config.google.firebase.sender_id,
    projectId: Laravel.config.google.firebase.project_id,
    apiKey: Laravel.config.google.firebase.api_key,
    appId: Laravel.config.google.firebase.app_id,
};
firebase.initializeApp(config);

let push = null;

try {
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey(Laravel.config.google.firebase.vapid_key);

    messaging.onTokenRefresh(function() {
        messaging.getToken().then(function(refreshedToken) {
            // console.log('Token refreshed.');
            // Indicate that the new Instance ID token has not yet been sent to the
            // app server.
            push.setTokenSentToServer(false);
            // Send Instance ID token to app server.
            push.sendTokenToServer(refreshedToken);
            // ...
        }).catch(function(err) {
            console.warn('Unable to retrieve refreshed token ', err);
        });
    });

    messaging.onMessage(function(payload) {
    });

    push = {
        messaging: messaging,
        requestPermission: () => {
            messaging.requestPermission().then(function() {
                messaging.getToken()
                    .then(function (currentToken) {
                        if (currentToken) {
                            push.sendTokenToServer(currentToken);
                        } else {
                            console.warn('Can\'t obtain token.');
                        }
                    })
                    .catch(function (err) {
                        console.warn('Token generation error.', err);
                    });

            }).catch(function(err) {
                console.warn('Unable to get permission to notify.', err);
            });
        },

        sendTokenToServer: function(currentToken) {
            if (!this.isTokenSentToServer(currentToken)) {
                // console.log('Send token to server...');

                axios.post(
                    zRoute('push-notifications.subscribe'),
                    {token: currentToken}
                ).then();

                this.setTokenSentToServer(currentToken);
            } else {
                // console.log('Token is already sent to server.');
            }
        },

        isTokenSentToServer: function(currentToken) {
            return window.localStorage.getItem('sentFirebaseMessagingToken') === currentToken;
        },

        setTokenSentToServer: function(currentToken) {
            window.localStorage.setItem(
                'sentFirebaseMessagingToken',
                currentToken ? currentToken : ''
            );
        },
    };
} catch (err) {
    // console.warn('Firebase messaging is not supported ', err);
}

export default push;
