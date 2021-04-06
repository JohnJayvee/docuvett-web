import axios from "axios";

/**
 * Returns TRUE: when error.response.status is contained in options.statusCodes
 * Returns FALSE: when error or error.response doesn't exist or options.statusCodes doesn't include response status
 */
export function shouldInterceptError(error, options, instance, cache) {
    if (error && error.config && error.config.skipAuthRefresh) {
        return false;
    }

    if (!error || !error.response || !options.statusCodes.includes(parseInt(error.response.status))) {
        return false;
    }

    return !cache.skipInstances.includes(instance);
}


/**
 * Creates refresh call if it does not exist or returns the existing one.
 *
 * @return Promise
 */
export function createRefreshCall(error, fn, cache) {
    if (!cache.refreshCall) {
        cache.refreshCall = fn(error);
    }

    return cache.refreshCall;
}


/**
 * Creates request queue interceptor if it does not exist and returns its id.
 *
 * @return Number
 */
export function createRequestQueueInterceptor(instance, cache, options) {
    if (typeof cache.requestQueueInterceptorId === 'undefined') {
        cache.requestQueueInterceptorId = instance.interceptors.request.use((request) => {
            return cache.refreshCall
                .catch(() => {
                    throw new axios.Cancel('Request call failed');
                })
                .then(() => options.onRetry ? options.onRetry(request) : request);
        });
    }
    return cache.requestQueueInterceptorId;
}


/**
 * Ejects request queue interceptor and unset interceptor cached values.
 */
export function unsetCache(instance, cache) {
    instance.interceptors.request.eject(cache.requestQueueInterceptorId);
    cache.requestQueueInterceptorId = undefined;
    cache.refreshCall = undefined;
    cache.skipInstances = cache.skipInstances.filter(skipInstance => skipInstance !== instance);
}

/**
 * Resend failed axios request.
 */
export function resendFailedRequest(error, instance) {
    error.config.skipAuthRefresh = true;

    return instance(error.response.config);
}