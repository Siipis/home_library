<template>
    <div class="books my-3">
        <div v-for="book in books" :key="book.id" class="book col-3 p-1">
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
    </div>
</template>

<script>
    // @link https://isotope.metafizzy.co/layout-modes/masonry.html

    // Load dependencies
    let jQueryBridget = require('jquery-bridget');
    let Isotope = require('isotope-layout');
    let imagesLoaded = require('imagesloaded');

    // Bind plugins to jQuery
    jQueryBridget('isotope', Isotope, $);
    imagesLoaded.makeJQueryPlugin($);

    export default {
        name: "Books",

        props: [
            'library', 'category'
        ],

        data() {
            return {
                paginator: [],
                $isotope: null,
            }
        },

        computed: {
            books: function () {
                return this.paginator.data;
            }
        },

        methods: {
            layout() {
                this.$isotope.isotope('layout');
            },

            loadBooks() {
                axios.post('', {
                    library: this.library,
                    category: this.category,
                }).then(({ data }) => {
                    this.error = {};
                    this.paginator = data;

                    this.layout();
                }).catch(({ response }) => {
                    this.error = response;
                })
            },
        },

        created() {
            this.loadBooks();
        },

        updated() {
            const vue = this;
            const grid = $('.books');

            this.$isotope = grid.isotope({
                itemSelector: '.book',
                layoutMode: 'masonry',
                masonry: {
                    columnWidth: '.book',
                    percentPosition: true,
                }
            });

            grid.imagesLoaded().progress(function () {
                vue.layout();
            })
        }
    }
</script>
