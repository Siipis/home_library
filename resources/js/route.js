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
