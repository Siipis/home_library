import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { RoutePlugin } from "./route";
import { LangPlugin } from "./localization";
import AjaxError from "./components/AjaxError";
import Books from "./components/Books";
import Category from "./components/Category";
import DeleteButton from "./components/DeleteButton";
import Flashable from "./components/Flashable";
import SearchResults from "./components/SearchResults";

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
window.Vue.component('books', Books);
window.Vue.component('category', Category);
window.Vue.component('delete-button', DeleteButton);
window.Vue.component('flashable', Flashable);
window.Vue.component('search-results', SearchResults);

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
    },

    methods: {
        fillForm: function(form_name, object) {
            const form = $("form[name='" + form_name + "']");
            const fields = form.find("[name^='" + form_name + "']");

            fields.each((i, field) => {
                const pattern = new RegExp("^" + form_name + "\\[(.*)\\]$");
                const name = $(field).attr('name').match(pattern)[1];

                if (object[name] !== undefined) {
                    $(field).val(Array.isArray(object[name]) ? object[name].join(', ') : object[name]);
                }
            });

            fields[0].focus();
        },
    },
});

/**
 * Add barcode scanner support.
 * @link https://github.com/axenox/onscan.js
 */

window.onScan = require('onscan.js');

window.onScan.attachTo(document, {
    suffixKeyCodes: [13],
    reactToPaste: false,
});
