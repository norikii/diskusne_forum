<template>
    <div>
        <!--@if (auth()->check())-->
        <!--<form style="margin-top: 20px" method="POST" action="{{ $thread->path() . '/replies' }}">-->
            <div v-if="signedIn">
                <div class="form-group">
                <textarea name="body"
                          rows="5"
                          class="form-control"
                          placeholder="Have something to say?"
                          required
                          v-model="body">
                </textarea>
                </div>
                <button type="submit"
                        class="btn btn-info"
                        @click="addReply">Post
                </button>
            </div>

        <p class="text-center" v-else>
            Please <a href="/login">sign in</a>
            to participate in this discussion.
        </p>

    </div>
</template>

<script>
    export default {
        name: "NewReply",
        computed: {
            signedIn() {
                return window.App.signedIn
            }
        },

        data() {
            return {
                body: '',
            }
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', { body: this.body })
                    // on successful response
                    .then(({data}) => {
                       this.body = '';

                       flash('Your reply has been posted.');

                       this.$emit('created', data);
                    });
            }
        }
    }
</script>

<style scoped>

</style>
