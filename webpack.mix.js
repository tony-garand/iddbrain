const mix = require('laravel-mix');
const webpack = require('webpack');

// mix.webpackConfig({
// 	plugins: [
// 		new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
// 	]
// });

// mix.disableNotifications();
mix.js([
		'resources/assets/js/app.js',
//		'resources/assets/js/moment.js',
		'node_modules/spectrum-colorpicker/spectrum.js',
		'resources/assets/js/datatable.js',
		'resources/assets/js/datatable-moment.js',
		'resources/assets/js/featherlight.js',
		'node_modules/lightbox2/dist/js/lightbox.js',
		'resources/assets/js/core.js'
	], 'public/js/app.js')
	.sass('resources/assets/sass/app.scss', 'public/css').version();