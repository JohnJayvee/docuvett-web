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
        name: "UIPlaceholder",
        components:{
        },
        props: [],
        data() {
            return {
                goodImg: window.Laravel.appUrl + '/images/ui-guide/input-field-placeholder-good.png',
                badImg: window.Laravel.appUrl + '/images/ui-guide/input-field-placeholder-bad.png',
                fit: 'contain'
            };
        },
        mounted() {
        },
        methods: {
        }
    };
</script>

