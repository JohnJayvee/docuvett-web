import moment from 'moment';

const defaultDates = {
    srvDateFormat: 'YYYY-MM-DD',
    srvTimeFormat: 'HH:mm:ss',
    get srvDateTimeFormat() {
        return this.srvDateFormat + ' ' + this.srvTimeFormat;
    },

    cliDateFormat: 'ddd DD/MMM/YYYY'.toLowerCase(),
    cliTimeFormat: 'hh:mm a',
    get cliDateTimeFormat() {
        return this.cliDateFormat + ' ' + this.cliTimeFormat;
    },
};
const keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789+/" +
               "=";

const messageTypes = [
    'success',
    'warning',
    'info',
    'error'
];

const defaultTerminologies = {
    Appointments: {
        module_title: "Appointments",
        page_title: "Appointments",
        singular_form: "Appointment"
    },
    Companies: {
        module_title: "Companies",
        page_title: "Companies",
        singular_form: "Company"
    },
    Events: {
        module_title: "Events",
        page_title: "Events",
        singular_form: "Event"
    },
    Sites: {
        module_title: "Sites",
        page_title: "Sites and Events",
        singular_form: "Site"
    }
};
let filters = {
    formatDate: function (text_date, only_date) {
        return methods.formatDate(text_date, false, only_date);
    },

    formatDateToDayDate: function (date) {
        return methods.formatDateToDayDate(date);
    },

    shortDateTime: function (text_date) {
        return methods.convertDate(text_date, defaultDates.srvDateTimeFormat, 'DD/MM/YYYY hh:mm a');
    },

    capitalize: function (value) {
        if (!value) return '';
        value = value.toString();
        return value.charAt(0).toUpperCase() + value.slice(1);
    },
    truncate: function (text, stop, clamp) {
        return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '');
    }
};

let methods = {
    moment,

    nl2br: function (value) {
        return value ? value.replace(/(?:\r\n|\r|\n)/g, '<br />') : '';
    },

    getQueryStringByName: function (name) {
        let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        let r = window.location.search.substr(1).match(reg);
        let context = "";
        if (r !== null)
            context = r[2];
        reg = null;
        r = null;
        return context === null || context === "" || context === "undefined" ? "" : decodeURIComponent(context);
    },

    convertDate: function (data, fromFormat, toFormat) {
        return data ? moment(data, fromFormat).format(toFormat) : '';
    },

    getFormat: function(data) {
        return moment(data).creationData().format;
    },

    formatDate: function (data, forDatabase, onlyDate) {
        let currentFormat = forDatabase ? defaultDates.cliDateTimeFormat : (onlyDate ? defaultDates.srvDateFormat : defaultDates.srvDateTimeFormat);
        let toFormat = forDatabase ? defaultDates.srvDateTimeFormat : (onlyDate ? defaultDates.cliDateFormat : defaultDates.cliDateTimeFormat);

        return methods.convertDate(data, currentFormat, toFormat);
    },
    convertDateToMoment: function (data, forDatabase, onlyDate) {
        let currentFormat = forDatabase ? defaultDates.cliDateTimeFormat : (onlyDate ? defaultDates.srvDateFormat : defaultDates.srvDateTimeFormat);
        return moment(data, currentFormat);
    },

    formatDateToHumanDate: function (date) {
        return this.convertDate(date,'YYYY-MM-DD HH:mm:ss', 'MMM DD');
    },

    formatToHours: function (date) {
        return this.convertDate(date, 'YYYY-MM-DD HH:mm:ss', 'HH:mm');
    },

    formatDateToDayDate: function (date, format, list = [], global) {
        if(format === list[0]){
            return this.convertDate(date, 'YYYY-MM-DD', global);
        }else {
            return this.convertDate(date,'YYYY-MM-DD HH:mm:ss', global+' hh:mm a');
        }
    },
    formatTime: function (time, format) {
        let parsedTime = time.split(':');
        let date = new Date(1970,1,1, parsedTime[0], parsedTime[1]);
        return moment(date).format(format);
    },
    addClassIfScroll: function (element, className) {
        if (element) {
            let hasScroll = element.scrollHeight > element.clientHeight;
            if (hasScroll) {
                element.className += " " + className;
            } else {
                element.classList.remove(className);
            }
        }
    },

    formatPhoneNumber: function (value) {
        const mask = [];
        const maxLength = 15; //for digits only
        let newValue = value.replace(/[^0-9+]/g, '').slice(0, maxLength);
        if (newValue !== '') {
            _.forEach(mask, (char, index) => {
                if (newValue[index] && newValue[index] !== char) {
                    newValue = newValue.slice(0, index) + char + newValue.slice(index);
                }
            });
        }
        return newValue;
    },

    formatTitleWithoutSpecialSymbols: function (value) {
        const mask = [];
        const maxLength = 255;
        let newValue = value.replace(/[`~!@#№$%^&*()_|+\-=?;:'",.<>{}\[\]\\\/]/gi, '').slice(0, maxLength);

        if (newValue !== '') {

            _.forEach(mask, (char, index) => {
                if (newValue[index] && newValue[index] !== char) {
                    newValue = newValue.slice(0, index) + char + newValue.slice(index);
                }
            });
        }
        return newValue;
    },

    validatePhoneNumber: function (rule, value, callback) {
        if (!value || value === '') {
            callback();
        }

        const validPhone = /^\+?61|64/;
        if (!validPhone.test(value)) {
            callback(new Error(rule.message ? rule.message : 'The phone must start with +61 or +64'));
        }

        const pattern = /^\+\d{11,15}/g;
        if (!pattern.test(value)) {
            callback(new Error(
                rule.message ?
                    rule.message :
                    'enter valid phone number (11 - 15 symbols, require +)'
            ));
        }

        callback();
    },

    validateFieldPrice: function (rule, value, callback) {
        if (!value || value === '') {
            callback();
        }

        if (isNaN(value)) {
            callback(new Error(
                rule.message ?
                    rule.message :
                    'product price is required'
            ));
        }

        if (value < 0.01) {
            callback(new Error(
                rule.message ?
                    rule.message :
                    'the number must be greater than 0.01'
            ));
        }

        callback();
    },

    scrollToTop: function () {
        let el = document.getElementById('scrollable-container');
        let currentScroll = el.scrollTop;
        if (currentScroll > 0) {
            window.requestAnimationFrame(methods.scrollToTop);
            el.scrollTo(0, currentScroll - (currentScroll / 3));
        }

    },

    createTextLinks: function (text) {
        return (text || "").replace(
            /([^\S]|^)(((https?:\/\/)|(www\.))(\S+))/gi,
            function (match, space, url) {
                let hyperlink = url;
                if (!hyperlink.match('^https?:\/\/')) {
                    hyperlink = 'http://' + hyperlink;
                }
                return space + '<a href="' + hyperlink + '" target="_blank">' + url + '</a>';
            }
        );
    },

    changeUserMessageCount: function (value) {
        let user = Vue.$auth.user();
        if (value === '++') {
            user.messages_incoming_count++;
        } else if (value === '--') {
            user.messages_incoming_count--;
        } else {
            user.messages_incoming_count = value;
        }
        Vue.$auth.user(user);
    },

    isMobile: function () {
        let check = false;
        (function (a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    },

    encode64: function (input) {
        input = escape(input);
        let output = "";
        let chr1, chr2, chr3 = "";
        let enc1, enc2, enc3, enc4 = "";
        let i = 0;

        do {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                keyStr.charAt(enc1) +
                keyStr.charAt(enc2) +
                keyStr.charAt(enc3) +
                keyStr.charAt(enc4);
            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";
        } while (i < input.length);

        return output;
    },

    decode64: function (input) {
        let output = "";
        let chr1, chr2, chr3 = "";
        let enc1, enc2, enc3, enc4 = "";
        let i = 0;

        // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
        let base64test = /[^A-Za-z0-9+\/=]/g;
        if (base64test.exec(input)) {
            // alert("There were invalid base64 characters in the input text.\n" +
            //     "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
            //     "Expect errors in decoding.");
        }
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        do {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";

        } while (i < input.length);

        return unescape(output);
    },

    handleResponseError: function (error) {
        if (window.Laravel.appDebug) {
            console.log(error);
        }
        if (error.response && error.response.data.message) {
            let type = error.response.data.type;
            if (messageTypes.includes(type)) {
                this.$message({
                    message: error.response.data.message,
                    type: type
                });
            } else {
                this.$message.error(error.response.data.message);
            }
        } else {
            this.$message.error('Unknown server error');
        }
    },
    /**
     * run this method on component mount to show edit popup
     * @param callableGetData callback returns data for edit function
     * @param routeName
     */
    listenSearchEvent(callableGetData, routeName) {
        Vue.$on('searchInit' + routeName, (id) => {
            callableGetData({id:id}).then((response) => {
                this.handleEdit.length === 2
                    ? this.handleEdit(id, response.data)
                    : this.handleEdit(response.data);
            });
        });

        let id = this.$route.params.searchId;

        if (id) {
            callableGetData({id:id}).then((response) => {
                this.handleEdit.length === 2
                    ? this.handleEdit(id, response.data)
                    : this.handleEdit(response.data);
            });
        }
    },

    pluck(array, key) {
        return array.map(o => o[key]);
    },

    getWeekPeriod(week) {
        if(week === 0) return 'This week';
        let startDate = moment();
        let endDate = moment();
        if(week > 0) {
            startDate.add(week, 'weeks');
            endDate.add(week, 'weeks');
        } else if(week < 0) {
            startDate.subtract(Math.abs(week), 'weeks');
            endDate.subtract(Math.abs(week), 'weeks');
        }

        return `${startDate.startOf('isoWeek').format('ll')} - ${endDate.endOf('isoWeek').format('ll')}`;
    },

    async checkModuleAccess(moduleName) {
        return await checkModuleAccessibility({module: moduleName}).then((response) => {
            if (response.data.message) {
                this.$message({
                    message: response.data.message,
                    type: 'warning'
                });
            }
            return response.data.status;
        });
    },

    fetchSystemMessages(){
        getSystemMessageList().then((response) => {
            response.data.forEach(message => {
                setTimeout( () => {
                    this.$notify({
                        duration: message.type === 'success-permanent' ? 0 : 5000,
                        title: 'New System Message',
                        message: '<div class="message-notification">' + this.createTextLinks(message.message) + '</div>',
                        type: message.type,
                        dangerouslyUseHTMLString: true
                    });
                }, 700);
            });
        });
    },

    //Terminology Manager methods
    fetchAllTerminologies() {
        getAllTerminologies().then((response) => {
            console.log(response.data);
            window.Laravel.TerminologyManager = response.data;
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
    },
    _getModuleDescriptionPart(module, part) {
        let terminology = window.Laravel.TerminologyManager,
            result;
        if (terminology.hasOwnProperty(module)) {
            result = terminology[module][part];
        } else if (defaultTerminologies.hasOwnProperty(module)) {
            result = defaultTerminologies[module][part];
        }

        return result ? result : module;
    },
    getSingularForm: module => methods._getModuleDescriptionPart(module, 'singular_form'),
    getModuleTitle: module => methods._getModuleDescriptionPart(module, 'module_title'),
    getPageTitle: module => methods._getModuleDescriptionPart(module, 'page_title'),
    mergeExistingFields(source, fields) {
        return _.assign(source, _.pick(fields, _.keys(source)));
    },
    async waitWhile(condition, callback, time = 10) {
        while (eval(condition)) {
            await new Promise(resolve => setTimeout(resolve, time));
        }
        callback();
    },
    findNextAvailableRouteName(sourceRouteName, routes) {
        const menuRoutes = _.find(routes, {menu: true}).children;
        const searchFromIndex = _.findIndex(menuRoutes, {name: sourceRouteName});
        const nextRoute = _.find(menuRoutes.slice(searchFromIndex + 1), (route) => {
            return !!route.component && !route.children &&
                (_.isEmpty(_.get(route, 'meta.auth')) || Vue.$auth.check(route.meta.auth));
        });
        return nextRoute.name;
    },
    numberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
};

let computed = {
    Laravel: () => {
        return window.Laravel;
    },
    //Terminology Manager
    ModuleTitle: function () {
        return this.getModuleTitle(this.$route.meta.module);
    },
    PageTitle: function () {
        return this.getPageTitle(this.$route.meta.module);
    },
    SingularForm: function () {
        return this.getSingularForm(this.$route.meta.module);
    },
    isAppointmentsAvailable() {
        return this.$store.state.Modules.availableList.Appointments;
    },
    isTagsAvailable() {
        return this.$store.state.Modules.availableList.Tags;
    },
};

export {filters, methods, computed, defaultDates};

export default methods;
