const FORMAT_DATETIME_SERVER = window.Laravel.formatDate.serverDateTime;
const FORMAT_DATE_DISPLAY = window.Laravel.formatDate.global;
const FORMAT_TIME_DISPLAY = window.Laravel.formatDate.globalTime;

export default {
    methods: {
        convertDateServerToDisplay(date, withTime) {
            const toFormat = withTime
                ? FORMAT_DATE_DISPLAY + ' ' + FORMAT_TIME_DISPLAY
                : FORMAT_DATE_DISPLAY;

            return moment(date, FORMAT_DATETIME_SERVER).format(toFormat);
        },
    },
};