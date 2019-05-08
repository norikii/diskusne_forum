<template>
    <li class="nav-item" v-if="notifications.length">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span><v-icon name="bell"></v-icon></span>
        </a>

        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" @click.prevent="markAsRead(notification)">notification.data.message</a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {

        data() {
            return {
                notifications: false,
                test: "/profiles/" + window.App.user.name + "/notifications"
            }
        },

        created() {
            axios.get("/profiles/" + window.App.user.name + "/notifications")
                .then(response => this.notifications = response.data);
        },

        methods: {
            markAsRead(notification) {
                axios.delete("/profiles/" + window.App.user.name + "/notifications/" + notification.id)
            }
        }

    }
</script>

<style scoped>

</style>
