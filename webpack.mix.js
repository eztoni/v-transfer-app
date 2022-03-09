const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/imports.js', 'public/js')

    //Chart JS
    .js('node_modules/chart.js/dist/chart.js',
        'public/packages/chartjs/chartjs.js')
    .js('resources/js/init-alpine.js',
        'public/js/init-alpine.js')

    .scripts('resources/js/mapInput.js',
        'public/js/mapInput.js')
    //Full Calendar
    .scripts('node_modules/fullcalendar/main.js',
        'public/packages/fullcalendar/calendar.js')
    .scripts('node_modules/fullcalendar/locales/hr.js',
        'public/packages/fullcalendar/hr.js')
    .css('node_modules/fullcalendar/main.css',
        'public/packages/fullcalendar/calendar.css')

    //Dropzone
    .scripts('node_modules/dropzone/dist/dropzone-min.js',
        'public/packages/dropzone/dropzone.js')
    .css('node_modules/dropzone/dist/dropzone.css',
        'public/packages/dropzone/dropzone.css')


    .sass('resources/css/styles.scss', 'public/css')


    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ]);

if (mix.inProduction()) {
    mix.version();
}
