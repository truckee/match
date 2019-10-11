/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');
//require('../full-width-pics-gh-pages/css/full-width-pics.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');
import './jquery-ui.min.js';
import '../css/app.scss';

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});
//console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
