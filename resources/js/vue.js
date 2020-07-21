import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { RoutePlugin } from "./route";
import { LangPlugin } from "./localization";
import AjaxError from "./components/AjaxError";
import DeleteButton from "./components/DeleteButton";

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
window.Vue.component('delete-button', DeleteButton);

window.app = new window.Vue({
    'el': '#app',

    mixins: window.Mixin.all(),

    data: () => ({
        token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        error: {},
    })
});
