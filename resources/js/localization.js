import Lang from 'lang.js';

export const messages = {
    'fi.auth': require('../lang/fi/auth.php'),
    'fi.fields': require('../lang/fi/fields.php'),
};

window.Lang = new Lang({
    locale: 'fi',
    messages
});

export const LangPlugin = {
    install: function (Vue, options) {
        Vue.filter('trans', function (key) {
            return window.Lang.get(key);
        })
    }
};
