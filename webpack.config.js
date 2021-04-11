const path = require('path');
const webpack = require('webpack');

module.exports = {
    output: {
        filename: '[name].js',
        publicPath: '/',
    },
    resolve: {
        alias: {
            '~': path.join(__dirname, './resources/assets/js'),
            '@': path.resolve('resources/assets/sass')
        }
    },
    plugins: [
        new webpack.ContextReplacementPlugin(
            /moment[\/\\]locale/,
            // A regular expression matching files that should be included
            /(en-au)\.js/
        )
    ],
    module: {
        rules: [
        ]
    }
};