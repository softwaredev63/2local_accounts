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

mix.js('resources/js/app.js', 'public/js').vue()
    .js('node_modules/pwstrength-bootstrap/dist/pwstrength-bootstrap.min.js', 'public/js/pwstrength.js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version()
    .webpackConfig({
        externals: {
            'jsdom': 'window',
        },
        resolve: {
            fallback: {
                "fs": false,
                "os": false,
                "https": false,
                "http": false,
                "crypto": false,
            },
        }
    });
