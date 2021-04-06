import {unsetCache, createRefreshCall, resendFailedRequest, shouldInterceptError, createRequestQueueInterceptor,} from "./utils";

const cache = {
    skipInstances: [],
    refreshCall: undefined,
    requestQueueInterceptorId: undefined,
};

const options = {
    statusCodes: [403]
};

const refreshAuthCall = async failedRequest => new Promise(async resolveMain => {
    const sessionExpiredAt = failedRequest.response.headers.session_expired_at;

    let gracePeriodSeconds = Number(failedRequest.response.headers.grace_period);

    if (!sessionExpiredAt || !!window.sessionNotification) return resolveMain();

    window.extendSession = () => {
        gracePeriodSeconds = 0;

        clearInterval(window.decrementInterval);

        const button = document.querySelector('.expired_session button.el-button');

        if (button instanceof HTMLElement) button.parentElement.removeChild(button);

        bus.$emit('auth:refresh');
    };

    const decrementGracePeriodHandler = () => {
        const button = document.querySelector('.expired_session button.el-button');

        if (!(button instanceof HTMLElement)) return;

        if (gracePeriodSeconds <= 1) {
            gracePeriodSeconds = 0;

            clearInterval(window.decrementInterval);

            return button.parentElement.removeChild(button);
        }

        button.querySelector('span').innerText = 'Continue (' + String(--gracePeriodSeconds) + ')';
    };

    let message = `<p>Your session expires at - ${sessionExpiredAt}. </p>`
        + '<p>You will be redirected to login form.</p>'
        + '<p>Please ensure that you had saved your changes.</p>';

    if (!isNaN(gracePeriodSeconds) && gracePeriodSeconds > 0) {
        message += `<p style="padding: 10px 0; text-align: center">
                        <button onclick="extendSession()" type="button" class="el-button el-button--success is-round el-button--small">
                            <span>Continue (${gracePeriodSeconds})</span>
                        </button>
                    </p>`;

        window.decrementInterval = setInterval(decrementGracePeriodHandler, 1000);
    }

    window.sessionNotification = window.Vue.$notify.warning({
        duration: 1000 * 60 * 10, // 10 min
        title: 'Your session will end soon',
        message,
        dangerouslyUseHTMLString: true,
        customClass: 'expired_session'
    });

    bus.$on('userLoggedOff', () => {
        if (window.sessionNotification) {
            window.sessionNotification.destroyElement();

            delete window.sessionNotification;

            clearInterval(window.decrementInterval);

            gracePeriodSeconds = 0;
        }
    });

    window.sessionNotification.$watch('visible', newVal => {
        if (newVal) return;

        window.sessionNotification.destroyElement();

        delete window.sessionNotification;

        clearInterval(window.decrementInterval);

        gracePeriodSeconds = 0;
    });

    return resolveMain();
});

export default function createAuthRefreshInterceptor(instance) {
    return instance.interceptors.response.use(response => response, error => {
        if (!shouldInterceptError(error, options, instance, cache)) {
            return Promise.reject(error);
        }

        cache.skipInstances.push(instance);

        // If refresh call does not exist, create one
        const refreshing = createRefreshCall(error, refreshAuthCall, cache);

        // Create interceptor that will bind all the others requests until refreshAuthCall is resolved
        createRequestQueueInterceptor(instance, cache, options);

        return refreshing
            .finally(() => unsetCache(instance, cache))
            .catch(error => Promise.reject(error))
            .then(() => resendFailedRequest(error, instance));
    });
}