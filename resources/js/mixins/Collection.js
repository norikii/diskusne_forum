// mixins in js are like traits in laravel
export default {
    data() {
        return {
            items: []
        }
    },

    methods: {
        add(item) {
            this.items.push(item);

            this.$emit('added');
        },

        remove(index) {
            // removes one item form the collection
            this.items.splice(index, 1);

            this.$emit('removed');
        }
    }
}
