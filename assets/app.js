/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import './styles/test.scss'
const $ = require('jquery');
// create global $ and jQuery variables
global.$ = global.jQuery = $;
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
const calendar = require('fullcalendar');
global.calendar = calendar;
import { Tooltip, Toast, Popover} from "bootstrap";

// start the Stimulus application
import './bootstrap';
import './main';
