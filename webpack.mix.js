const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    resolve: {
        alias: {
            'bootstrap-vue$': 'bootstrap-vue/src/index.js',
        }
    },

    module: {
        rules: [
            {
                // Matches all PHP or JSON files in `resources/lang` directory.
                test: /resources[\\\/]lang.+\.(php|json)$/,
                loader: 'laravel-localization-loader',
            },
        ]
    },
});

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/vue.js', 'public/js')
    .extract([
        'axios',
        'vue',
        'bootstrap-vue',
        'popper.js',
        'lang.js',
        'tinycolor2',
        'jquery',
        'onscan.js',
        'lodash',
    ])
    .sass('resources/sass/app.scss', 'public/css');

if (mix.inProduction()) {
    mix.version();
} else {
    mix.browserSync({
        proxy: process.env.APP_URL,
        files: [
            './app',
            './config',
            './routes',
            './resources',
        ],
        localOnly: true,
    });
}
