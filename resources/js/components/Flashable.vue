<template>
    <b-alert dismissible fade :variant="variant"
             :show="dismissCountDown"
             @dismissed="dismissCountDown=0"
             @dismiss-count-down="countDownChanged">
        <slot></slot>
    </b-alert>
</template>

<script>
    export default {
        name: "Flashable",

        props: {
            variant: {
                type: String,
                required: true,
            }
        },

        data() {
            return {
                dismissSecs: 10,
                dismissCountDown: 0,
                showDismissableAlert: true,
            }
        },

        methods: {
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
        },

        mounted() {
            this.dismissCountDown = this.dismissSecs;
        }
    }
</script>
