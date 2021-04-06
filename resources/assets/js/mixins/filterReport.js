import {editFilter, getFilterList, removeFilter} from "~/includes/_common_/endpoints";
import Errors from "~/includes/_common_/Errors";
import {defaultDates} from "~/includes/_common_/util";
import moment from 'moment';

const initialFilter = {
    id: '',
    name: '',
    target: '',
    is_widget: 0,
    data: {
        dateRange: [
            moment().startOf('month').format(defaultDates.srvDateFormat + ' HH:mm:ss'),
            moment().endOf('month').format(defaultDates.srvDateFormat + ' HH:mm:ss'),
        ],
        month: null,
        site: '',
        site_id: '',
        site_event_id: '',
        user_id: '',
        created_at_from: '',
        created_at_to: '',
        consultant: '',
        group: '',
        state: '',
        locality: '',
        groupBy: 'user',
        sortBy: 'id,asc',
    },
};

export const filterReport = {
    data() {
        return {
            filter: _.cloneDeep(initialFilter),
            filters: [],
            errors: new Errors(),
            formFilterVisible: false,
        };
    },
    methods: {
        fetchFilters() {
            getFilterList({target: this.filterTarget}).then((response) => {
                this.filters = response.data;
                if (this.isWidget) {
                    let filter = _.find(this.filters, 'is_widget');
                    if (filter) {
                        this.filter = filter;
                    }
                    this.fetchReport();
                }
            });
        },
        clearFilter() {
            this.filter = _.cloneDeep(initialFilter);
            this.fetchReport();
        },
        setFilter(filter) {
            this.formFilterVisible = false;
            this.filter = _.cloneDeep(filter);
            this.fetchFilters();
            this.fetchReport();
        },
        setDashboardFilter() {
            if (this.filter.id) {
                this.filter.is_widget = 1;
                editFilter(this.filter).then((response) => {
                    this.$message.success(response.data.message);
                    this.filter = response.data.filter;
                    this.fetchFilters();
                }).catch((error) => {
                    if (Laravel.appDebug) {
                        console.log(error);
                    }
                    if (error.response.data.errors) {
                        this.errors.record(error.response.data.errors);

                    } else if (error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Unknown server error');
                    }
                });
            }
        },
        handleSaveFilter() {
            this.errors = new Errors();
            this.formFilterVisible = true;
            if (this.$refs['filter_form']) {
                this.$refs['filter_form'].clearValidate();
            }
        },
        deleteFilter() {
            if (this.filter.id) {
                this.$confirm('This will permanently delete the filter "' + this.filter.name + '". Continue?', 'Warning', {
                    type: 'warning'
                }).then(() => {
                    this.dataLoading = true;
                    removeFilter(this.filter.id).then((response) => {
                        this.$message.success(response.data.message);
                        this.clearFilter();
                        this.fetchFilters();
                    }).catch((error) => {
                        this.dataLoading = false;
                        if (error.response.data.message) {
                            this.$message.error(error.response.data.message);
                        } else {
                            this.$message.error('Unknown server error');
                        }
                    });
                }).catch(() => {
                });
            }
        },
    },
    mounted() {
        this.filter.target = this.filterTarget;
        this.fetchFilters();
    },
};