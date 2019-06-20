<template>
    <div class="text-center text-background">
        <b-row align-h="start">
            <b-col cols="2">
            <router-link :to="{ name: 'homepage' }" v-on:click.native="deleteUserFromRoom">
                Leave room
            </router-link>
            </b-col>
        </b-row>

        <div v-if="userData.id_users == roomData.id_users_owner">

            <b-row align-h="end" class="adminSettings">


            <b-col cols="4">
                <div v-if="roomData.lock == false">
                    <b-button type="submit" variant="info" class="btn btn-sm" v-on:click="lockRoom">
                        <font-awesome-icon icon="lock-open"/> Lock room
                    </b-button>
                </div>
                <div v-else>
                    <b-button type="submit" variant="info" class="btn btn-sm" v-on:click="unlockRoom">
                        <font-awesome-icon icon="lock"/> Unlock room
                    </b-button>
                </div>
            </b-col>
            <b-col cols="2">
            <b-button type="submit" variant="danger" class="btn btn-sm" v-on:click="deleteRoom">

            <font-awesome-icon icon="trash"/> Delete room
        </b-button>
            </b-col>
            </b-row>
    </div>

        <b-row align-h="between" class="mt-5">
            <b-col cols="5">
                <h1>Room {{roomData.title}}</h1>
                <b>Owner:</b>
                <div v-for="user in usersData">
                    <div v-if="user.id_users == roomData.id_users_owner">
                        {{user.login}}
                    </div>
                </div>

                <br>
                <b>Users in the room:</b>
                <div v-for="user in usersInRoom">
                    <b-row align-h="center">
                        <div class="mb-1 mt-1 mr-1">
                    {{user.login}}
                        </div>
                    <div v-if="userData.id_users == roomData.id_users_owner && userData.id_users != user.id_users">
                        <b-button type="submit" variant="danger" class="btn btn-sm" v-on:click="kickUser(user.id_users)">
                        <font-awesome-icon icon="user-minus"/>
                        </b-button>
                    </div>
                    </b-row>
                    </div>
            </b-col>
            <b-col cols="7">
        <div v-if="messages.length > 0" class="chat" id="style-5">
            <div v-for="message in messages">
        <div class="person1 text-left mr-5 mt-2" v-if="message.login == userData.login">
           <b>{{message.login}}</b>: {{message.message}}
        </div>
            <div class="person2 text-right mr-5 mt-2" v-else>
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
                userData: null,
                usersData: [],
                roomData: null
            }
        },
        created () {
            window.addEventListener('beforeunload', this.deleteUserFromRoom)
        },

        // po nacteni komponenty
        mounted() {
            const roomId = this.$route.params.id;
            this.roomId = roomId;

            this.user();

            this.users();

            this.room();

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

            deleteUserFromRoom() {
                this.$http.delete('api/auth/deleteUserFromRoom/' + this.roomId)
                    .then(() => {
                        this.$router.push({name: 'rooms'});
                    }).catch(() => {
                    alert("Error deleting user");
                })
            },

            user() {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.userData = response.data;
                    })
            },

            users() {
                this.$http.get('api/auth/users')
                    .then(response => {
                        this.usersData = response.data;
                    })
            },

            room() {
                this.$http.get('api/auth/rooms/' + this.roomId)
                    .then(response => {
                        this.roomData = response.data;
                    })
            },

            kickUser(id) {
                this.$http.post('/api/auth/kickUser', {
                    roomId: this.roomId,
                    userId: id
                }).then(() => {
                    }
                ).catch(() => {
                    alert("Error kicking user");
                })
            },
            lockRoom() {
                this.$http.put('/api/auth/lockRoom', {
                    roomId: this.roomId
                }).then(() => { window.location.reload();
                    }
                ).catch(() => {
                    alert("Error while locking room");
                })
            },
            unlockRoom() {
                this.$http.put('/api/auth/unlockRoom', {
                    roomId: this.roomId
                }).then(() => { window.location.reload();
                    }
                ).catch(() => {
                    alert("Error while unlocking room");
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
        font-size: 18px;
    }

    .chat {
        font-size: 14px;
        font-weight: normal;
        max-height: 300px;
        overflow: auto;
        margin-right: 0.3em;
    }

    .person1 {
        background:rgba(255,255,255, 0.8);
        padding: 0.5em;
        border-radius: 10px;
    }

    .person2 {
        background:rgba(255,255,255, 0.6);
        padding: 0.5em;
        border-radius: 10px;
    }

    .chatInput {
        font-size: 14px;
    }

    #style-5::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #F5F5F5;
    }

    #style-5::-webkit-scrollbar
    {
        width: 10px;
        background-color: #F5F5F5;
        width: 0.5em;
    }

    #style-5::-webkit-scrollbar-thumb
    {
        background-color: #0ae;

        background-image: -webkit-gradient(linear, 0 0, 0 100%,
        color-stop(.5, rgba(255, 255, 255, .2)),
        color-stop(.5, transparent), to(transparent));
    }

    .adminSettings {
        margin-top: -1.5em;
    }

</style>
