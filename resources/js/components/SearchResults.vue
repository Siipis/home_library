<template>
    <b-table striped hover
             :fields="fields"
             :items="items">
        <template v-slot:cell(selector)="book">
            <b-button variant="outline-secondary" class="bg-white" size="sm" @click="$root.$emit('select_book', book.item)">
                <b-icon icon="plus"></b-icon>
            </b-button>
        </template>
    </b-table>
</template>

<script>
    export default {
        name: "SearchResults",

        props: [
            'items', 'onClick'
        ],

        data() {
            return {
                fields: [
                    {
                        key: 'selector',
                        label: '',
                    },
                    {
                        key: 'title',
                        label: Lang.get('fields.title'),
                        sortable: true,
                    },
                    {
                        key: 'authors',
                        label: Lang.get('fields.authors'),
                        sortable: true,
                        formatter: (authors) => (authors.join('; ')),
                    },
                    {
                        key: 'year',
                        label: Lang.get('fields.year'),
                        sortable: true,
                    },
                    {
                        key: 'isbn',
                        label: Lang.get('fields.isbn'),
                        formatter: function (isbn, key, book) {
                            if (isbn === null) return isbn;

                            if (book.other_isbn.length > 0) {
                                return isbn + '*';
                            }

                            return isbn;
                        },
                    }
                ],
            }
        },
    }
</script>

<style scoped>

</style>
