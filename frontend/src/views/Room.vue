<template>
    <div class="text-center text-background">
        <b-row align-h="between">
            <b-col cols="2">
            <router-link :to="{ name: 'homepage' }" v-on:click.native="deleteUserFromRoom">
                X
            </router-link>
            </b-col>
            <b-col cols="4">
                <b-form-checkbox>Private</b-form-checkbox>
            </b-col>
            <b-col cols="2">
            <b-button type="submit" variant="danger" class="btn btn-sm" v-on:click="deleteRoom">
            <font-awesome-icon icon="trash"/>
        </b-button>
            </b-col>
        </b-row>
        <b-row align-h="between" class="mt-5">
            <b-col cols="5">
                <h1>Room {{title}}</h1>
                Users in the room:
                <div v-for="user in usersInRoom">
                    {{user.login}}
                </div>
            </b-col>
            <b-col cols="7">
        <div v-if="messages.length > 0">
            <div v-for="message in messages">
        <div class="chat1 text-left mr-5 mt-2" v-if="message.login == userData.login">
           <b>{{message.login}}</b>: {{message.message}}
        </div>
            <div class="chat text-right mr-5 mt-2" v-else>
                <b>{{message.login}}</b>: {{message.message}}
            </div>
            </div>
    </div>
    <div v-else>
        No messages yet
    </div>
        <b-form @submit.prevent="saveMessage">
        <b-row align-h="end" class="mt-2 mr-5">
            <b-col cols="10">
        <b-form-input class="chatInput"
                id="message"
                v-model="text"
                type="text"
                required
                placeholder="Write your message"
        ></b-form-input>
            </b-col>
            <b-col cols="2">
                <b-button type="submit" variant="info" class="btn btn-md chatInput">
                    Send
                </b-button>
            </b-col>
        </b-row>
        </b-form>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "Room",
        data() {
            return {
                roomId: null,
                messages: [],
                usersInRoom: [],
                reloader: null,
                title: null,
                userData: null
            }
        },
        // po nacteni komponenty
        mounted() {
            const roomId = this.$route.params.id;
            this.roomId = roomId;

            const title = this.$route.params.title;
            this.title = title;

            this.saveUserInRoom();

            this.user();

            this.reloader = setInterval(() => {
                this.loadMessages();
                this.inRoom();
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
            },

            deleteRoom() {
                this.$http.delete('api/auth/deleteRoom/' + this.roomId)
                .then(() => {
                    alert("Room deleted");
                    this.$router.push({name: 'rooms'});
                }).catch(() => {
                    alert("Error");
                })
            },

            saveMessage() {
                this.$http.post('/api/auth/saveMessage', {
                    roomId: this.roomId,
                    text: this.text
                }).then(() => {
                        this.text = "";
                    }
                ).catch(() => {
                    alert("Error");
                })
            },

            inRoom() {
                this.$http.get('api/auth/users/' + this.roomId)
                    .then(response => {
                        this.usersInRoom = response.data;
                    })
                },

            saveUserInRoom() {
                this.$http.post('/api/auth/saveUserInRoom', {
                    roomId: this.roomId
                }).then(() => {
                    }
                ).catch(() => {
                    alert("Error");
                })
            },

            deleteUserFromRoom() {
                this.$http.delete('api/auth/deleteUserFromRoom/' + this.roomId)
                    .then(() => {
                        this.$router.push({name: 'rooms'});
                    }).catch(() => {
                    alert("Error");
                })
            },

            user: function () {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.userData = response.data;
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

    .chat {
        font-size: 14px;
        font-weight: normal;
        background:rgba(255,255,255, 0.6);
        border-radius: 10px;
        padding: 0.5em;
    }

    .chat1 {
        font-size: 14px;
        font-weight: normal;
        background:rgba(255,255,255, 0.9);
        border-radius: 10px;
        padding: 0.5em;
    }

    .chatInput {
        font-size: 14px;
    }

</style>
