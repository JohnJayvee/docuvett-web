export default class ErrorHandler {
    constructor(errorsInstance) {
        this._instance = errorsInstance._instance;

        this.errorsInstance = errorsInstance;
    }

    async handle(error) {
        try {
            this.debugError(error);

            if (!this.isServerError(error)) return;

            const {errors = null, message = null} = error.response.data;

            this._instance.$message.error(message || 'Unknown server error');

            await this._instance.$nextTick();

            if (this.errorsBagFilled(errors)) {
                this.errorsInstance.record(errors);
            }

            await this.errorsInstance._vm.$nextTick();
        } catch (e) {
            this.debugError(e);

            console.error(error);
        }

        await this._instance.$nextTick();
    }

    debugError(error) {
        window.Laravel && window.Laravel.appDebug && console.log(error);
    }

    isServerError(error) {
        return !!error && typeof error === 'object' && !!error.response && !!error.response.data;
    }

    errorsBagFilled(errorsBag) {
        return errorsBag && typeof errorsBag === 'object' && Object.values(errorsBag).length > 0;
    }
}