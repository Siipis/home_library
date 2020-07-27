<template>
    <div class="books my-3 d-flex flex-row flex-wrap">
        <b-card v-for="book in books"
                class="text-center m-1 col-md-3 col-sm-6"
                :title="book.title"
                :img-src="book.cover"
        img-top>
            {{ book.authors }}
        </b-card>
    </div>
</template>

<script>
    export default {
        name: "Books",

        props: [
            'library', 'category'
        ],

        data() {
            return {
                paginator: [],
            }
        },

        computed: {
            books: function() {
                return this.paginator.data;
            }
        },

        created() {
            axios.post('', {
                library: this.library,
                category: this.category,
            }).then(({ data }) => {
                this.error = {};
                this.paginator = data;
            }).catch(({ response }) => {
                this.error = response;
            })
        }
    }
</script>
