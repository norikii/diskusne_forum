<template>
    
</template>

<script>
    import SubscribeButton from './SubscribeButton';
    import Replies from './Replies';

    export default {
        name: "Thread",

        props: ['initialRepliesCount'],
        // child component
        components: { SubscribeButton, Replies },

        data() {
            return {
                repliesCount: this.initialRepliesCount
            }
        }
    }
</script>

<style scoped>

</style>
