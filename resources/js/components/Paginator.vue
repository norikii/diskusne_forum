<template>
    <ul class="pagination mt-5" v-if="shouldPaginate">
        <li class="page-item" v-show="prevUrl">
            <a class="page-link" href="#" tabindex="-1" @click.prevent="page--">Previous</a>
        </li>
        <li class="page-item" v-show="nextUrl">
            <a class="page-link" href="#" @click.prevent="page++">Next</a>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "Paginator",
        props: ['dataSet'],

        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false
            }
        },
        // keep an eye on dataSet property
        // and when it changes we want to change the state of our
        // data()
        watch: {
            dataSet() {
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },

            page() {
                this.broadcast().updateUrl();
            },
        },

        computed: {
            shouldPaginate() {
                return !! this.prevUrl || !! this.nextUrl;
            }
        },

        methods: {
            broadcast() {
                return this.$emit('changed', this.page);
            },

            updateUrl() {
                history.pushState(null, null, '?page=' + this.page);
            }
        }

    }
</script>

<style scoped>

</style>
