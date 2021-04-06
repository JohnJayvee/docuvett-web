import vm from './vm';
import ErrorHandler from './Handler';

export default class Errors {
    constructor(instance, Vue) {
        // Component instance
        this._instance = instance;

        // Vue instance for reactivity
        this._vm = vm(Vue);

        this.errorhandler = new ErrorHandler(this);
    }

    /**
     * Get all the errors.
     *
     * @return Object
     */
    all() {
        return this._vm.getAllErrors();
    }

    /**
     * Determine if any errors exists for the given field or object.
     *
     * @param {string} field
     *
     * @return Boolean
     */
    has(field) {
        return this._vm.hasError(field);
    }

    /**
     * Determine if we have any errors.
     *
     * @return Boolean
     */
    any() {
        return this._vm.anyError();
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     * @param {*} defaultValue
     */
    get(field, defaultValue = null) {
        return this._vm.getError(field, defaultValue);
    }

    /**
     * Set the error message for a field.
     *
     * @param {string} field
     * @param {string|Array} error
     */
    set(field, error) {
        this._vm.setError(field, error);
    }

    /**
     * Record the new errors.
     *
     * @param {Object} errors
     */
    record(errors) {
        this._vm.record(errors);
    }

    /**
     * Clear a specific field, object or all error fields.
     *
     * @param {string|null} field
     */
    clear(field) {
        this._vm.clear(field);
    }

    /**
     * Called when component destroyed
     */
    destroy() {
        this._vm && this._vm.destroy();
    }

    /**
     * Handle error
     */
    async handle(e) {
        await this.errorhandler.handle(e);
    }

    /**
     * Call callback when determined error for field
     *
     * @param {string} field
     * @param {function} cb
     */
    onHasError(field, cb) {
        this._vm.onHasError(field, cb);
    }

    /**
     * Call callback when determined error for any of fields
     *
     * @param {string|Array} fields
     * @param {function} cb
     */
    onHasAnyError(fields, cb) {
        this._vm.onHasAnyError(fields, cb);
    }

    /**
     * Print first of exists error
     *
     * @param {function|null} printCb
     */
    printFirstError(printCb = null) {
        if (!this.any()) {
            return;
        }

        const printFunction = typeof printCb === 'function'
            ? printCb
            : this._instance.$message.error;

        for (let field in this.all()) {
            return printFunction(
                this.get(field)
            );
        }
    }

    /**
     * await vue instances updating
     *
     * @returns
     */
    async $nextTick() {
        await this._instance.$nextTick();

        await this._vm.$nextTick();
    }
}