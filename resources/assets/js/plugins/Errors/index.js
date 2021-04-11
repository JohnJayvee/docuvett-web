import Errors from './Errors';

export default {
    install(Vue) {
        Vue.mixin({
            beforeDestroy() {
                if (this.$data._$errors instanceof Errors) {
                    this.$data._$errors.destroy();
                }
            }
        });

        Object.defineProperties(Vue.prototype, {
            $errors: {
                get: function () {
                    if (this.$data._$errors instanceof Errors) {
                        return this.$data._$errors;
                    }

                    this.$data._$errors = new Errors(this, Vue);

                    const methods = ['all', 'has', 'any', 'get', 'set', 'record', 'clear', 'destroy', 'handle', 'printFirstError'];

                    methods.forEach(m => {
                        this.$data._$errors[m] = this.$data._$errors[m].bind(this.$data._$errors);
                    });

                    return this.$data._$errors;
                },
                set: function () {
                    console.error('Don\'t set $errors property manually');
                }
            }
        });
    }
};