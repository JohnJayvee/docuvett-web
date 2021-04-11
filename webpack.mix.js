let mix            = require('laravel-mix');
const argv         = require('yargs').argv;
const inProduction = mix.inProduction();
const config       = require('./webpack.config');

mix
    .webpackConfig(config)
    .js(config.resolve.alias["~"] + '/app.js', 'public/js/compiled')
    .extract()
    .sass(config.resolve.alias["@"] + '/app.scss', 'public/styles/compiled/sass_part.css', {
        implementation: require('node-sass')
    })
    .autoload({
        lodash: ['_'],
        axios: ['Axios'],
    })
;

if (inProduction) {
    mix.version();
} else {
    mix.sourceMaps();
}

if (argv.bundle_analyzer) {
    require('laravel-mix-bundle-analyzer');
    mix.bundleAnalyzer();
}

// mix.options({
//     extractVueStyles: true,
//     postCss: [require('less-plugin-sass2less/lib/index.js')],
// });

mix.config.imgLoaderOptions = {
    enabled: inProduction,
    optipng: true,
    mozjpeg: {
        quality: 70
    }
};
mix.config.fileLoaderDirs.images = 'images/compiled';

// mix.options({ processCssUrls: false });