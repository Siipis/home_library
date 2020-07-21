<template>
    <b-alert variant="danger" :show="message.length > 0">
        <p>{{ message }}</p>

        <ul v-if="errors.length > 0">
            <li v-for="(detail, index) in errors" :key="index">
                {{ detail }}
            </li>
        </ul>
    </b-alert>
</template>

<script>
    export default {
        name: "AjaxError",

        props: {
            error: {
                type: Object,
                default: {}
            },
        },

        computed: {
            message() {
                if (this.error.data !== undefined) {
                    if (this.error.data.message !== undefined) {
                        return this.error.data.message;
                    }
                }

                return '';
            },

            errors() {
                if (this.error.data !== undefined) {
                    if (this.error.data.errors !== undefined) {
                        return Object.values(this.error.data.errors).flat();
                    }
                }

                return [];
            },
        },
    }
</script>
