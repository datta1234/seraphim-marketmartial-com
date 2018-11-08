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

mix.js('resources/assets/js/trade-screen.js', 'public/js').version();
mix.js('resources/assets/js/canvas.js', 'public/js').version();
mix.js('resources/assets/js/public.js', 'public/js').version();
mix.sass('resources/assets/sass/app.scss', 'public/css').version();