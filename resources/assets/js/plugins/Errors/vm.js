export default function (Vue) {
    return new Vue({
        data() {
            return {
                errors: {}
            };
        },
        methods: {
            getError(field, defaultValue = null) {
                if (!Object.keys(this.errors).includes(field)) {
                    return defaultValue;
                }

                if (typeof this.errors[field] === 'string') {
                    return this.errors[field];
                }

                return this.errors[field][0] || defaultValue;
            },
            setError(field, error) {
                this.$set(this.errors, field, error);
            },
            record(errors) {
                this.errors = errors;
            },
            clear(field) {
                this.errors = field === undefined ? {} : _.omit(this.errors, field);
            },
            getAllErrors() {
                return this.errors;
            },
            hasError(field) {
                let hasError = this.errors.hasOwnProperty(field);

                return hasError || Object
                    .keys(this.errors)
                    .filter(e => e.startsWith(`${field}.`))
                    .length > 0;
            },
            anyError() {
                return Object.keys(this.errors).length > 0;
            },
            destroy() {
                this.$destroy();
            },

            onHasError(field, cb) {
                if (typeof field !== 'string') {
                    return console.error('{field} must be string', {field});
                }

                if (typeof cb !== 'function') {
                    return console.error('{cb} must be function', {cb});
                }

                const expression = `errors.${field}`,
                    watchers = this._watchers.map(w => w.expression);

                if (watchers.includes(expression)) {
                    return;
                }

                this.$watch(expression, (newVal, oldVal) => {
                    if (!!newVal && !oldVal) {
                        cb(this.getError(field));
                    }
                });
            },
            onHasAnyError(fieldsArr, cb) {
                const fields = Array.isArray(fieldsArr)
                    ? fieldsArr
                    : typeof fieldsArr === 'string' ? fieldsArr.split('|') : [];

                if (fields.length === 0) {
                    return console.error('{fieldsArr} must be array or string with delimiter "|"', {fieldsArr});
                }

                if (typeof cb !== 'function') {
                    return console.error('{cb} must be function', {cb});
                }

                this.$watch('errors', (newVal, oldVal) => {
                    if (typeof newVal !== 'object' || typeof oldVal !== 'object') return;

                    for (let field in newVal) {
                        if (!newVal.hasOwnProperty(field)) {
                            continue;
                        }

                        if (!fields.includes(field)) {
                            continue;
                        }

                        if (!oldVal.hasOwnProperty(field)) {
                            return cb(field, this.getError(field));
                        }
                    }
                }, {deep: true});
            }
        }
    });
}