module.exports = {
    data() {
        return {
            multipleSelectOptionsSelector: '.el-select-dropdown.is-multiple .el-select-dropdown__list *',
        };
    },
    methods: {
        addDoubleClickListener() {
            document.addEventListener('dblclick', (e) => {
                if (this.isMultipleOptionElement(e.target)) {
                    const optionComponent = this.getOptionComponent(e.target);
                    if (optionComponent) {
                        optionComponent.select.handleClose();
                    }
                }
            });
            document.addEventListener('click', (e) => {
                if (this.shouldStopClickPropagation(e)) {
                    e.stopPropagation();
                }
            }, true);
        },
        shouldStopClickPropagation(e) {
            if (!this.isMultipleOptionElement(e.target)) {
                return false;
            }
            if (e.detail <= 1) {
                return false;
            }
            return true;
        },
        isMultipleOptionElement(target) {
            return target && target.matches(this.multipleSelectOptionsSelector);
        },
        getOptionComponent(target) {
            if (!target) {
                return null;
            }
            if (target.matches('.el-select-dropdown__item')) {
                return target.__vue__;
            }
            return this.getOptionComponent(target.parentElement);
        },
    },
    mounted() {
        this.addDoubleClickListener();
    },
};