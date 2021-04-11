import { getUserAutocomplete, } from "~/includes/_common_/endpoints";

export const handleScrollUsers = {
    data() {
        return {

        };
    },
    created() {
        bus.$on('closeTeamForm', () => {
            if (this.$refs['form']) {
                this.$refs['form'].resetFields();
            }
            this.users = [];
            this.usersCurrentPage = 1;
        });
        bus.$on('fetchData', (data) => {
            if (data && data.row_users) {
                this.users = data.row_users;
            }
            data.row_users = null;
            this.$set(this, 'form', data);
        });
    },
    methods: {
        listenerScrollUsers() {
            let scrollNode = document.querySelector('.users-list .el-checkbox-group');
            scrollNode.addEventListener('scroll', this.handleScrollUsers, false);
        },
        handleScrollUsers() {
            if ((this.users.length >= (+this.usersTotalPages + +this.form.to_ids.users.length) && this.usersTotalPages !== 0)
                || this.recipientFilters.users.search.length) {
                return false;
            }

            let scrollNode = document.querySelector('.users-list .el-checkbox-group');

            if (this.isDownEndList(scrollNode)) {
                this.usersCurrentPage++;
                this.fetchUsers();
            }
        },
        isDownEndList(node) {
            return  Math.ceil(node.offsetHeight + node.scrollTop) >= node.scrollHeight;
        },
        fetchUsers(query) {
            if (this.$auth.check('users.index')) {
                let usersIds = this.form.to_ids.users.length ? this.form.to_ids.users : [];

                let params = {
                    page: this.usersCurrentPage,
                    search: query,
                    sortBy: 'name,asc',
                    pageSize: this.recipientFilters.users.search.length ? null : this.amountShowUsers,
                    usersIds: this.recipientFilters.users.search.length ? null : usersIds,
                    rolesName: this.searchUserRole,
                };

                this.recipientListLoading = true;
                getUserAutocomplete(
                    params,
                    {}
                ).then((res) => {

                    // collect users added to form
                    let addedUsers = _.filter(this.users, (user) => { return (_.indexOf(this.form.to_ids.users, user.key) + 1); });
                    const newUsers = [];

                    let resultArrayData = res.data.data
                        ? res.data.data
                        : res.data;

                    resultArrayData.forEach((user) => {
                        newUsers.push({
                            key: user.id,
                            label: user.name,
                            email: user.email,
                            phone: user.phone,
                            duplicate: false
                        });
                    });

                    let fullUsersList =[];
                    if (!(query && query.length)) {
                        fullUsersList = _.concat(this.users, newUsers); // concat current users with loaded
                    } else {
                        fullUsersList = _.concat(addedUsers, newUsers); // replace users list with filtered one
                    }

                    this.$set(this, 'users', _.uniqBy(fullUsersList, 'key'));

                    this.usersTotalPages = res.data.total || 0;
                }).finally(() => {
                    this.recipientListLoading = false;
                });
            }
        },
    },
    watch: {
        'recipientFilters.users.search': _.debounce(function (search) {
            this.fetchUsers(search);
        }, 300),
        'form.to_ids.users': function(val) {
            if ((+val.length !== 0 && +this.users.length !== 0) && ((+this.users.length - +val.length) <= 5)) {
                this.usersCurrentPage = 1;
                this.fetchUsers();
            }
        },
    },
};