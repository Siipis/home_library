import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Route, { RoutePlugin } from "./route";
import { LangPlugin } from "./localization";
import AjaxError from "./components/AjaxError";
import DeleteConfirmation from "./components/DeleteConfirmation";

require('./bootstrap');

/**
 * Import helper classes.
 */
window.Route = new Route();

/**
 * Import Vue and configure it.
 */
window.Vue = require('vue');

window.Vue.options.delimiters = [ '[[', ']]' ];

window.Vue.use(BootstrapVue);
window.Vue.use(IconsPlugin);

window.Vue.use(RoutePlugin);
window.Vue.use(LangPlugin);

window.Vue.component('ajax-error', AjaxError);
window.Vue.component('delete-confirmation', DeleteConfirmation);

window.app = new window.Vue({
    'el': '#app',
    data: () => ({
        token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    })
});
