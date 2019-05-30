<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="dark" fixed="top">
            <b-navbar-brand href="/">Home</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>


                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">

                    <div v-if="isLoggedIn">
                        <b-nav-item-dropdown right>
                            <template slot="button-content">User</template>
                            <b-dropdown-item v-on:click="logout">Log out</b-dropdown-item>
                        </b-nav-item-dropdown>
                    </div>
                    <div v-else>
                        <b-button v-on:click="login">Log in</b-button>
                    </div>
                </b-navbar-nav>
        </b-navbar>
        <div class="background"></div>
    </div>
</template>

<script>
    export default {
        name: "NavBar",
        computed: {
            isLoggedIn: function () {
                const token = localStorage.getItem('token');
                return token !== null && token !== undefined;
            }
        },
        methods: {
            login: function () {
                this.$router.push({name: 'login'});
            },
            logout: function () {
                localStorage.removeItem('token');
                window.location.reload();
            }
        }
    }
</script>

<style scoped>

    .background {
        width: 100%;
        height: 100%;
        z-index: -1;
        position: fixed;
        margin-top: -4em;
        background-image: url("../assets/background.jpg");
        background-size: cover;
        filter: blur(2px) grayscale(70%);
        transform: scale(1.03);
    }

</style>
