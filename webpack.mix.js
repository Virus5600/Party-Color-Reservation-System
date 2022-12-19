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
        devtool: 'inline-source-map'
    })
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/scripts.js', 'public/js')
    // FullCalendar
    .scripts(['node_modules/fullcalendar/main.min.js', 'node_modules/fullcalendar/locales/ja.js'], 'public/js/lib/fullcalendar/fullcalendar.js')
    .postCss('node_modules/fullcalendar/main.min.css', 'public/css/lib/fullcalendar/fullcalendar.css')
    .react()
    .sourceMaps()
    .disableNotifications();