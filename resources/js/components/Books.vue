<template>
    <div class="books my-3">
        <b-card v-for="book in books" :key="book.id"
                class="text-center col-3 d-inline-block"
                :img-src="book.cover"
        img-top>
            <b-card-body>
                <b-card-title>
                    <a :href="book.link">{{ book.title }}</a>
                </b-card-title>
                <b-card-sub-title>
                    {{ book.authors }}
                </b-card-sub-title>
            </b-card-body>
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
