<template>
    <div class="text-center text-background">
        <div class="text-left ml-5">
            <router-link :to="{ name: 'homepage' }">
                X
            </router-link>
        </div>
        <h1>Room {{roomId}}</h1>
        <div v-for="message in messages">
            {{message.message}}
        </div>
    </div>
</template>

<script>
    export default {
        name: "Room",
        data() {
            return {
                roomId: null,
                messages: [],
                reloader: null,
                title: null
            }
        },
        // po nacteni komponenty
        mounted() {
            const roomId = this.$route.params.id;
            this.roomId = roomId;

            this.reloader = setInterval(() => {
                this.loadMessages();
            }, 1000); // zavola metodu pro stazeni dat kazdou vterinu
        },
        // pred zrusenim komponenty
        beforeDestroy() {
            clearInterval(this.reloader);
        },
        methods: {
            loadMessages() {
                this.$http.get('api/auth/messages/' + this.roomId)
                    .then(response => {
                        this.messages = response.data;
                    })
            }
        }
    }
</script>

<style scoped>

    .text-background {
        background:rgba(255,255,255, 0.6);
        margin-top: 7em;
        margin-bottom: 7em;
        padding-top: 2em;
        padding-bottom: 3em;
        border-radius: 6px;
        font-weight: bold;
    }

</style>
