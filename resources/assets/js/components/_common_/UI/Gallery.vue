<template>
    <section>
        <template
            v-for="(url, index) in images">
            <p :key="index + 222">
                <el-image
                    :key="index + 100"
                    :src="url"
                    :fit="fit"></el-image>
            </p>
        </template>
    </section>
</template>

<style lang="scss" scoped>
</style>

<script>
    export default {
        name: "UIGallery",
        components:{
        },
        props: [],
        data() {
            return {
                url: window.Laravel.appUrl,
                images: [
                    window.Laravel.appUrl + '/images/ui-guide/gallery/appt-appts-tab.jpg',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/appt-setup.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/companies-activity-tab.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/companies-status-dropdown.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/edit-product-features-tab.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/invoices-page-title.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/invoices-tab-heading.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/order-new-details-tab.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/order-page-heading.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/orders-tab-padding.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/preferences-orders-tabs.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/teamwork-tasklist-form.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/teamwork-tasks.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/teamwork-tasks-form.png',
                    window.Laravel.appUrl + '/images/ui-guide/gallery/xero-settings-el-card.png',
                ],
                fit: 'contain'
            };
        },
        mounted() {
        },
        methods: {
        }
    };
</script>

