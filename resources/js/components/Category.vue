<template>
    <a v-if="isLink" :href="href" :class="classes" :style="styles">
        <slot></slot>
    </a>
    <div v-else :class="classes" :style="styles">
        <slot></slot>
    </div>
</template>

<script>
    // @link https://github.com/bgrins/TinyColor
    const tinycolor = require('tinycolor2');

    export default {
        name: "Category",

        props: [ 'color', 'size', 'href' ],

        computed: {
            isLink: function() {
                return this.href !== undefined;
            },

            classes: function () {
                return {
                    category: true,
                    lg: this.size === 'lg',
                    sm: this.size === 'sm',
                    xs: this.size === 'xs',
                };
            },

            styles: function () {
                const color = tinycolor(this.color);

                return {
                    background: color.toString(),
                    borderColor: color.toString(),
                    color: (color.isDark() ? tinycolor('white') : tinycolor('black')).toString(),
                }
            },
        }
    }
</script>
