<template>
    <section>
        <el-row :gutter="20">
            <el-col :sm="12" :lg="6">
                <div class="grid-content bg-purple"></div>
            </el-col>
            <el-col :sm="12" :lg="6">
                <div class="grid-content bg-purple"></div>
            </el-col>
            <el-col :sm="12" :lg="6">
                <div class="grid-content bg-purple"></div>
            </el-col>
            <el-col :sm="12" :lg="6">
                <div class="grid-content bg-purple"></div>
            </el-col>
        </el-row>
    </section>
</template>

<style lang="scss" scoped>
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .bg-purple-dark {
    background: #99a9bf;
  }
  .bg-purple {
    background: #d3dce6;
  }
  .bg-purple-light {
    background: #e5e9f2;
  }
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }

  @media only screen and (max-width: 1200px) {
    .grid-content {
      margin-bottom: 20px;
    }
  }
</style>

<script>
    export default {
        name: "UIResponsiveness",
        components:{
        },
        props: [],
        data() {
            return {
            };
        },
        mounted() {
        },
        methods: {
        }
    };
</script>

