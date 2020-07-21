export default class Mixins {
    constructor() {
        this.mixins = [];
    }

    add(name, mixin) {
        this.mixins[name] = mixin;
    }

    has(name) {
        return this.mixins[name] !== undefined;
    }

    get(name) {
        return this.mixins[name];
    }

    all() {
        return Object.values(this.mixins);
    }
}
