let mix = require('laravel-mix');

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
mix;

mix.webpackConfig({
    resolve: {
      alias: {
        '~': path.resolve(__dirname, 'resources/assets/js')
      }
    }
});

mix.options({
    // this is taking too many resources atm - need to look at fix
    uglify: false
})

mix.js('resources/assets/js/trade-screen.js', 'public/js')
mix.js('resources/assets/js/canvas.js', 'public/js')
mix.js('resources/assets/js/public.js', 'public/js')
mix.js('resources/assets/js/previous-day.js', 'public/js')
mix.js('resources/assets/js/activity-log.js', 'public/js')
mix.sass('resources/assets/sass/app.scss', 'public/css');

if(process.env.NODE_ENV == 'production') {
    mix.version();
}