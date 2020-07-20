<template>
    <div>
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
            <b-button variant="danger" @click="$refs[modalId].show()">
                <b-icon icon="x"></b-icon>
            </b-button>
        </form>
    </div>
</template>

<script>
    export default {
        name: "DeleteConfirmation",
        props: ['action', 'id'],
        computed: {
            modalId: function () {
                return 'delete-' + this.id;
            },

            formId: function () {
                return 'form-delete-' + this.id;
            }
        }
    }
</script>

<style scoped>

</style>
