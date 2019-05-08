<template>
    <!--{{&#45;&#45; v-cloak adds the attribute only when everything has been loaded &#45;&#45;}}-->
        <div :id="'reply-'+id" style="margin-top: 20px" class="card">
            <div class="card-header">
                <div class="level">
                    <h5 class="flex">
                        <a :href="'/profiles/'+data.owner.name" v-text="data.owner.name">
                        </a> said <span v-text="ago"></span>
                    </h5>


                    <div v-if="signedIn" class="border">
                        <favorite :reply="data"></favorite>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body"></textarea>
                    </div>

                    <button class="btn btn-sm btn-primary" @click="update">Update</button>
                    <button class="btn btn-sm btn-danger" @click="editing = false">Cancel</button>
                </div>
                <div v-else v-text="body"></div>
            </div>

            <div class="card-footer level" v-if="canUpdate">
                <button class="btn btn-primary btn-sm mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-sm mr-1" @click="destroy">Delete</button>
            </div>

        </div>
</template>

<script>
    import Favorite from './Favorite';
    import moment from 'moment';

    export default {
        name: "Reply",
        props: ['data'],
        components: { Favorite },
        data() {
            return {
                editing: false,
                body: this.data.body,
                id: this.data.id
            };
        },
        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },
            signedIn() {
                return window.App.signedIn;
            },
            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id);
                // return this.data.user_id ==  window.App.user.id;
            }
        },

        methods: {
            update() {
                // on button click update we submit ajax request to patch to the endpoint
                // and we send through the updated body
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                });

                this.editing = false;

                flash('Updated!')
            },

            destroy() {
                axios.delete('/replies/' + this.data.id);

                // this is how we communicate with the parent elemnt
                // by pushing event to it
                this.$emit('deleted', this.data.id);

                // $(this.$el).fadeOut(300, () => {
                //     flash('Your reply has been deleted!');
                // });
            }
        }
    }
</script>

<style scoped>

</style>
