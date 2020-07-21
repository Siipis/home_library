export default class Route {
    get(route, replacements) {
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
        const route = new Route();

        Vue.route = route.get;
    }
};
