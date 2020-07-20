export default class Route {
    static get(route, replacements) {
        let uri = route.trim('/');

        Object.keys(replacements).forEach(replace => {
            const original = '{' + replace + '}';
            const intended = replacements[replace];

            uri = uri.replace(original, intended);
        });

        return '/' + uri;
    }
}

export const RoutePlugin = {
    install: function (Vue, options) {
        Vue.route = Route.get;
    }
};
