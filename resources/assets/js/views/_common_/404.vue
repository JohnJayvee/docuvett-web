<template>
    <el-container class="login-screen background-image">
        <el-main class="login-section">
            <div class="v-align-container">
                <span class="error-code">404</span>
                <br>
                <span class="error-info">page not found<br><router-link :to="{name: 'Dashboard'}">go&nbsp;home</router-link></span>
            </div>
        </el-main>
    </el-container>
</template>

<style lang="scss" scoped>
    @import "../../../sass/element-ui-colors";
    @import "../../../sass/element-ui-variables";

    .login-screen {
        min-height: 100%;
        &.background-image {
            background-image: url("../../../img/header-image.jpg");
            background-size: cover;
            background-position: center;

            &:before {
                background-image: repeating-radial-gradient(circle at center, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4) 2px, transparent 2px, transparent 100%);
                background-size: 6px 6px;
            }
        }
        &:before {
            content: '';
            position: absolute;
            height: 100%;
            width: 100%;
            left: 0;
            min-height: 600px;
            @media (max-width: $--xs - 1) {
                min-height: 500px;
            }
        }

        .login-section {
            padding: 0;
            min-height: 600px;
            display: flex;
            align-items: center;
            align-content: center;
            @media (max-width: $--xs - 1) {
                min-height: 500px;
                padding-top: 80px;
            }
        }

        .v-align-container {
            width: 100%;
            text-align: center;
            z-index: 10;
            color: $--color-white;
        }
        .error-code {
            color: inherit;
            font-size: calc(8vh + 7vw);
            font-weight: bold;
            text-shadow: 2px 2px 9px #000000;
        }
        .error-info {
            color: inherit;
            font-size: calc(3vh + 2vw);
            text-transform: uppercase;
            text-shadow: 2px 2px 9px #000000;
            line-height: calc(5vh + 4vw);
            a {
                text-decoration: none;
                color: inherit;
                border: 3px solid white;
                padding: 0 20px;
                border-radius: 100px;
                box-shadow: 2px 2px 9px black;
            }
        }
    }
</style>