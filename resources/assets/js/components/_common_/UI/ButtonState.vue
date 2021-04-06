<template>
    <section>
        <h3>Incorrect</h3>
        <p>
            <el-image
                :src="badImg"
                :fit="fit"></el-image>
        </p>

        <h3>Correct</h3>
        <el-image
            :src="goodImg"
            :fit="fit"></el-image>
    </section>
</template>

<style lang="scss" scoped>
</style>

<script>
    export default {
        name: "UIButtonState",
        components:{
        },
        props: [],
        data() {
            return {
                goodImg: window.Laravel.appUrl + '/images/ui-guide/button-state-good.png',
                badImg: window.Laravel.appUrl + '/images/ui-guide/button-state-bad.png',
                fit: 'contain'
            };
        },
        mounted() {
        },
        methods: {
            handleConfirmation() {
                this.$confirm(`You are about to do something dangerous. Continue?`, 'Warning', {
                    type: 'warning'
                }).then(() => {

                });
            },
        }
    };
</script>

