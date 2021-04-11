<template>
    <section>
        <h2 class="page-title">
            Appointments
        </h2>
        <breadcrumb></breadcrumb>
        <div class="error-box"></div>
        <el-tabs value="Schedule">
            <el-tab-pane label="Schedule" name="Schedule">
                <appointment-board manager-id="1" :is-call-center="true"></appointment-board>
            </el-tab-pane>
            <el-tab-pane label="Appointments" name="Appointments">
                <el-row>
                    <el-col :span="24" class="m-t-10 m-b-30" style="padding-bottom: 0px;">
                        <el-tabs value="UNCONFIRMED">
                            <el-tab-pane label="UNCONFIRMED" name="UNCONFIRMED">
                                <appointment-list
                                    ref="unconfirmedAppointments"
                                    :can-create="false"
                                    :completed="false"
                                    :confirmed="false"
                                    :unconfirmed="true"></appointment-list>
                            </el-tab-pane>
                            <el-tab-pane label="CONFIRMED" name="CONFIRMED">
                                <appointment-list
                                    ref="confirmedAppointments"
                                    :can-confirm="false"
                                    :can-create="false"
                                    :completed="false"
                                    :confirmed="true"
                                    :set-month="confirmedMonth"
                                    :set-period="confirmedPeriod"
                                    :unconfirmed="false"></appointment-list>
                            </el-tab-pane>
                            <el-tab-pane label="COMPLETED" name="COMPLETED">
                                <appointment-list
                                    ref="completedAppointments"
                                    :can-confirm="false"
                                    :can-create="false"
                                    :completed="true"
                                    :confirmed="false"
                                    :unconfirmed="false"></appointment-list>
                            </el-tab-pane>
                        </el-tabs>
                    </el-col>
                </el-row>
            </el-tab-pane>
        </el-tabs>
    </section>
</template>

<style lang="scss" scoped>

</style>

<script>
    import Breadcrumb from "../../../views/Admin/Breadcrumb";

    export default {
        name: "UIListingHeaderBad",
        components:{
            Breadcrumb,
        },
        props: [],
        data() {
            return {
                confirmedPeriod: 'week',
                confirmedMonth: false,
            };
        },
        mounted() {
        },
        methods: {
            handleAdd() {

            },
            applySearch() {

            },
            clearSearch() {

            },
        }
    };
</script>

