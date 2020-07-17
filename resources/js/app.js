import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Route from "./route";
import AjaxError from "./components/AjaxError";

require('./bootstrap');

/**
 * Import helper classes.
 */
window.Route = new Route();

/**
 * Import Vue and configure it.
 */
window.Vue = require('vue');

window.Vue.options.delimiters = ['[[', ']]'];

window.Vue.use(BootstrapVue);
window.Vue.use(IconsPlugin);

window.Vue.component('ajax-error', AjaxError);
