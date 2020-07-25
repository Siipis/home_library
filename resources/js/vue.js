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

    data: {
        token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        error: {},
    },

    mounted: function() {
        document.addEventListener('scan', function (event) {
            const target = event.target.activeElement;

            if (target.__vue__ !== undefined) {
                target.__vue__.$emit('scan', event.detail.scanCode);
            }
        });
    }
});

/**
 * Add barcode scanner support.
 * @link https://github.com/axenox/onscan.js
 */

window.onScan = require('onscan.js');

window.onScan.attachTo(document, {
    suffixKeyCodes: [13],
    reactToPaste: true,
});
