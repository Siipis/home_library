<template>
    <div class="delete-button">
        <b-modal centered no-stacking :ref="modalId"
                 header-bg-variant="danger"
                 header-text-variant="light"
                 :title="'fields.are_you_sure'|trans"
                 ok-variant="danger"
                 :ok-title="'fields.delete'|trans"
                 cancel-variant="secondary"
                 :cancel-title="'fields.cancel'|trans"
                 @ok="$refs[formId].submit()">
            <slot></slot>
        </b-modal>

        <form method="post" :action="action" :ref="formId">
            <input type="hidden" name="_token" :value="$root.token">
            <input type="hidden" name="_method" value="delete">
            <b-button :variant="variant" :size="size" @click="$refs[modalId].show()">
                <b-icon :icon="icon"></b-icon>
            </b-button>
        </form>
    </div>
</template>

<script>
    export default {
        name: "DeleteButton",

        props: {
            action: {
                type: String,
            },
            id: {
                type: String,
            },
            variant: {
                type: String,
                default: 'danger',
            },
            icon: {
                type: String,
                default: 'x',
            },
            size: {
                type: String,
            }
        },

        computed: {
            modalId: function () {
                if (this.id === undefined) {
                    return 'delete';
                }

                return 'delete' + this.id;
            },

            formId: function () {
                if (this.id === undefined) {
                    return 'form-delete';
                }

                return 'form-delete-' + this.id;
            }
        }
    }
</script>

<style scoped>

</style>
