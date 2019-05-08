<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <!-- when paginator fires the event called updated we will refetch all data we required by fetch function -->
        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <new-reply class="mt-3" @created="add"></new-reply>
    </div>
</template>

<script>
    import Reply from './Reply'
    import NewReply from './NewReply'
    import collection from '../mixins/Collection'

    export default {
        name: "Replies",
        // props: ['data'],
        components: { NewReply, Reply },
        mixins: [collection],

        data() {
            return {
                dataSet: false,
            };
        },

        // when the component is created fetch the data
        created() {
          this.fetch();
        },

        methods: {
            // fetch the data by performing the axios call
            fetch(page) {
                axios.get(this.url(page))
                    .then(this.refresh);
            },

            // we default the page to 1 to prevent page = null
            url(page ) {
                if (! page) {
                    let query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : 1;
                }

                return `${location.pathname}/replies?page=${page}`;
            },

            // once you have response from the fetch refresh the component
            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;

                window.scrollTo(0, 0);
            },
        }
    }
</script>

<style scoped>

</style>
