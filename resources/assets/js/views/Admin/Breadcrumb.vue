<template>
    <el-breadcrumb separator="/" class="breadcrumb">
        <el-breadcrumb-item v-for="(breadcrumb, idx) in breadcrumbList" :key="idx" :to="{ name: breadcrumb.name }">
            {{ breadcrumb.title }} <span v-if="name && breadcrumbList.length-1 === idx">{{ name }}</span>
        </el-breadcrumb-item>
    </el-breadcrumb>
</template>

<script>
    export default {
        name: "Breadcrumb",
        props: ['name'],
        data() {
            return {
                breadcrumbList: [],
            };
        },
        mounted() {
            this.updateList();
        },
        methods: {
            updateList() {
                this.breadcrumbList = this.$route.meta.breadcrumb;
            }
        }
    };
</script>

<style lang="scss" scoped>
    .breadcrumb {
        padding: 10px 0 10px 0;
    }
</style>