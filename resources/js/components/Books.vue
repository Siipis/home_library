<template>
    <isotope :options="options" :list="allBooks" class="books my-3"
             ref="grid" v-images-loaded:on.progress="layout"
             v-infinitescroll="loadBooks" infinite-scroll-disabled="loading">
        <div v-for="book in allBooks" :key="book.id" class="book col-3 p-1">
            <b-card class="book-card text-center"
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
    </isotope>
</template>

<script>
    // @link https://isotope.metafizzy.co/layout-modes/masonry.html

    // Load dependencies
    import isotope from 'vueisotope';
    import imagesLoaded from 'vue-images-loaded';
    import infinitescroll from 'vue-infinite-scroll';

    export default {
        name: "Books",

        props: {
            paginator: {
                required: true
            },
        },

        data() {
            return {
                loading: false,
                loadedBooks: [],
                next_page_url: this.paginator.next_page_url,
                options: {
                    itemSelector: '.book',
                    layoutMode: 'masonry',
                    masonry: {
                        columnWidth: '.book',
                        percentPosition: true,
                        horizontalOrder: true,
                    }
                },
            }
        },

        computed: {
            allBooks() {
                if (this.paginator.data === undefined) {
                    return this.loadedBooks;
                }

                return this.paginator.data.concat(this.loadedBooks);
            }
        },

        methods: {
            layout() {
                this.$refs.grid.layout('masonry');
            },

            loadBooks() {
                if (this.next_page_url === null) return;

                this.loading = true;

                axios.post(this.next_page_url).then(({ data }) => {
                    this.error = {};
                    this.next_page_url = data.next_page_url;
                    this.loadedBooks = this.loadedBooks.concat(data.data);
                }).catch(({ response }) => {
                    this.error = response;
                }).then(() => {
                    this.loading = false;
                });
            },
        },

        directives: {
            imagesLoaded, infinitescroll
        },

        components: {
            isotope
        },
    }
</script>
