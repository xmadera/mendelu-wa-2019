<template>
    <div class="text-center text-background">
        <b-row align-h="start">
            <b-col cols="2">
            <router-link :to="{ name: 'rooms' }" v-on:click.native="handlerLeave(userData.id_users)">
                Leave room
            </router-link>
            </b-col>
        </b-row>


        <b-row align-h="between" class="mt-5">
            <b-col md="5" cols="12">
                <h1>Room {{roomData.title}}</h1>
                <div v-if="roomData.lock" class="mb-1">
                    (Locked)
                </div>


                <template v-if="userData.id_users == roomData.id_users_owner">

                    <b-row align-h="center">

                        <b-col cols="4">
                            <div v-if="!roomData.lock" class="mb-3">
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
                            <b-col cols="4">

                            <div v-if="usersInRoom.length == 1">
                                <b-button type="submit" variant="danger" class="btn btn-sm" v-on:click="deleteRoom">
                                    <font-awesome-icon icon="trash"/> Delete room
                                </b-button>
                            </div>
                            <div v-else>
                                <b-button type="submit" variant="danger" class="btn btn-sm" disabled>
                                    <font-awesome-icon icon="trash"/> Delete room
                                </b-button>
                            </div>
                        </b-col>
                    </b-row>
                </template>


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
                        <b-button type="submit" variant="danger" class="btn btn-sm" v-on:click="handler(user.id_users)">
                        <font-awesome-icon icon="user-minus"/>
                        </b-button>
                    </div>
                    </b-row>
                    </div>
                <br>
                <div v-if="userData.id_users == roomData.id_users_owner">
                <b>Kicked users:</b>
                <div v-for="user in usersData">
                    <b-row align-h="center">
                    <div v-if="isKicked(user.id_users)">
                    {{user.login}}
                        <b-button type="submit" variant="info" class="btn btn-sm" v-on:click="removeKick(user.id_users)">
                            <font-awesome-icon icon="user-plus"/>
                        </b-button>
                    </div>
                    </b-row>
                </div>
                    <div v-if="kickData.length == 0">
                        None
                    </div>
                </div>
            </b-col>
            <b-col md="7" cols="12" class="mt-2">
        <div v-if="messages.length > 0" class="chat" id="style-5">
            <div v-for="message in messages">
        <div class="toAll text-left mt-2" v-if="message.id_users_to == null && message.id_users_from == userData.id_users">
            {{message.created.substring(11,16)}}<b> {{message.login}}</b>: {{message.message}}
        </div>
                <div class="whisper text-left mt-2" v-else-if="message.id_users_to != null && message.id_users_to != userData.id_users">
                    {{message.created.substring(11,16)}} <b> {{message.login}}</b>: {{message.message}}
                </div>
                <div class="whisper text-right mt-2" v-else-if="message.id_users_to == userData.id_users">
                {{message.created.substring(11,16)}} <b> {{message.login}}</b>: {{message.message}}
            </div>
            <div class="toAll text-right mt-2" v-else-if="message.id_users_to == null">
                {{message.created.substring(11,16)}} <b> {{message.login}}</b>: {{message.message}}
            </div>
            </div>
    </div>
    <div v-else>
        No messages yet
    </div>
        <b-form @submit.prevent="saveMessage()">
        <b-row align-h="end" class="mt-2">
            <b-col cols="7">
        <b-form-input class="chatInput"
                id="message"
                v-model="text"
                type="text"
                required
                placeholder="Write your message"
        ></b-form-input>
            </b-col>
                <b-col cols="3">
                    <span>To: </span>
                <b-form-select v-model="selected">
                    <template slot="first">
                        <option :value="null">All</option>
                    </template>
                <option v-for="user in usersInRoom" v-if="user.id_users != userData.id_users" :value="user.id_users">{{user.login}}</option>
                </b-form-select>
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
                selected: null,
                roomId: null,
                messages: [],
                usersInRoom: [],
                reloader: null,
                reloaderremoveUser: null,
                reloaderRemoveRoom: null,
                userData: '',
                usersData: [],
                roomData: '',
                text: null,
                kickData: [],
                possibleOwners: [],
                userMessages: []
            }
        },

        mounted() {
            const roomId = this.$route.params.id;
            this.roomId = roomId;

            this.user();
            this.room();
            this.inRoom();


            this.reloader = setInterval(() => {
            this.Kicks();
            this.inRoom();
            this.isAllowed();


            this.users();
            this.room();
            this.loadMessages();

            }, 1000); // Load data every 1000 ms

            // Remove user after 30 min of inactivity
            this.reloaderRemoveUser = setInterval(() => {
                let time = 1000 * 60 * 60;
                var k=0;
                if (this.messages.length > 0) {

                    for (let i = 0; i < this.messages.length; i++) {
                        if (this.messages[i].id_users == this.userData.id_users) {
                            this.userMessages[k] = this.messages[i];
                            k++;
                        }
                    }

                    let minus = Date.now() - 216000 - Date.parse(this.userMessages[this.userMessages.length-1].created);

                    if (minus > time) {
                        this.deleteUserFromRoom(this.userData.id_users);
                        this.$router.push({name: 'rooms'});
                        alert("You have been kicked from " + this.roomData.title +" due to inactivity");
                        clearInterval(this.reloaderRemoveUser);
                    }
                }
            }, 1001 * 60 * 60);

            // Delete room after 12 hours of inactivity
            this.reloaderRemoveRoom = setInterval(() => {
                let time = 1000 * 60 * 60 * 12;
                    if (this.messages.length > 0) {
                        let minus = Date.now() - 216000 - Date.parse(this.messages[this.messages.length - 1].created);

                        if (minus > time) {
                            this.deleteRoom();
                            clearInterval(this.reloaderRemoveRoom);
                        }
                    } else {
                        this.deleteRoom();
                        clearInterval(this.reloaderRemoveRoom);
                    }
            }, 1001 * 60 * 60 * 12);


    },
        beforeDestroy() {
            clearInterval(this.reloader);
        },
        methods: {
            // load all messages for this room
            loadMessages() {
                this.$http.get('api/auth/messages/' + this.roomId)
                    .then(response => {
                        this.messages = response.data;
                    })
            },

            deleteRoom() {
                // Delete this room
                this.$http.delete('api/auth/deleteRoom/' + this.roomId)
                .then(() => {
                    clearInterval(this.reloaderRemoveRoom);
                    clearInterval(this.reloaderRemoveUser);
                    alert("Room deleted");
                    this.$router.push({name: 'rooms'});
                }).catch(() => {
                    alert("Error deleting room");
                })
            },
            // save message for this room
            saveMessage() {
                this.$http.post('/api/auth/saveMessage', {
                    roomId: this.roomId,
                    text: this.text,
                    recipient: this.selected
                }).then(() => {
                    this.loadMessages();
                    this.text = "";
                    }
                ).catch(() => {
                    alert("Error");
                })
            },
            // Select all users in the room
            inRoom() {
                this.$http.get('api/auth/users/' + this.roomId)
                    .then(response => {
                        this.usersInRoom = response.data;
                    })
                },
            // Delete selected user from this room
            deleteUserFromRoom(id) {
                this.$http.post('/api/auth/deleteUserFromRoom', {
                    roomId: this.roomId,
                    userId: id
                }).then(() => {
                    clearInterval(this.reloaderRemoveUser);
                    }
                ).catch(() => {
                    alert("Error deleting user from room");
                })
            },
            // Kick selected user from this room
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
            // Remove kick from selected user for this room
            removeKick(id) {
                this.$http.post('/api/auth/removeKick', {
                    roomId: this.roomId,
                    userId: id
                }).then(() => {
                    }
                ).catch(() => {
                    alert("Error removing kick");
                })
            },
            // Load data about user
            user() {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.userData = response.data;
                    })
            },
            // Load data about all users
            users() {
                this.$http.get('api/auth/users')
                    .then(response => {
                        this.usersData = response.data;
                    })
            },
            // Load data about this room
            room() {
                this.$http.get('api/auth/rooms/' + this.roomId)
                    .then(response => {
                        this.roomData = response.data;
                    })
            },
            // Lock this room
            lockRoom() {
                this.$http.put('/api/auth/lockRoom', {
                    roomId: this.roomId
                }).then(() => { window.location.reload();
                    }
                ).catch(() => {
                    alert("Error while locking room");
                })
            },
            // Unlock this room
            unlockRoom() {
                this.$http.put('/api/auth/unlockRoom', {
                    roomId: this.roomId
                }).then(() => { window.location.reload();
                    }
                ).catch(() => {
                    alert("Error while unlocking room");
                })
            },
            // Load data about all kicks
            Kicks() {
                this.$http.get('api/auth/kicks', {
                })
                    .then(response => {
                        this.kickData = response.data;
                    })
            },
            // Determine if user is allowed to be in this room
            isAllowed() {
                if (this.roomData.lock == true) {
                    for (let i = 0; i < this.usersInRoom.length; i++) {
                        if (this.usersInRoom[i].id_users == this.userData.id_users) {
                            return;
                        }
                    }
                    this.$router.push({name: 'rooms'});
                    alert("This room is locked");
                    return;
                }

                for (let i = 0; i < this.kickData.length; i++) {
                    if (this.kickData[i].id_rooms == this.roomId && this.kickData[i].id_users == this.userData.id_users) {
                        this.$router.push({ name: 'rooms' });
                        alert("You have been kicked");
                    }
                }
                },
            // Handle kicking user out of this room
            handler: function(id) {
                this.deleteUserFromRoom(id);
                this.kickUser(id);

                this.timer = setTimeout(() => {
                    this.removeKick(id)
                }, 300000);
            },
            // Handle user leaving this room
            handlerLeave: function(id) {
                this.deleteUserFromRoom(id);
                if (id == this.roomData.id_users_owner) {
                    this.ownerLeaves();
                }
            },
            // determine if user is kicked out of this room
            isKicked(id) {
                for (let i = 0; i < this.kickData.length; i++) {
                    if (this.kickData[i].id_rooms == this.roomId && this.kickData[i].id_users == id) {
                        return true
                    }
                }
                return false;
            },
            // Updates owner if owner leaves this room
            updateOwner(id) {
                this.$http.put('/api/auth/updateOwner', {
                    roomId: this.roomId,
                    userId: id
                }).then(() => {
                    }
                ).catch(() => {
                    alert("Error while updating owner");
                })
            },
            // Determine if owner leaves this room
            ownerLeaves() {
                    if (this.usersInRoom.length > 0) {
                        for (let i = 0; i < this.usersInRoom.length; i++) {
                             this.possibleOwners[i] = this.usersInRoom[i].id_users;
                        }
                        let rand = this.possibleOwners[Math.floor(Math.random() * this.possibleOwners.length)];
                        this.updateOwner(rand);
                }
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
        white-space: nowrap;
    }

    .chat {
        font-size: 14px;
        font-weight: normal;
        height: 330px;
        overflow: auto;
        margin-right: 0.3em;
        background:rgba(51,51,51,0.3);
        padding: 0.4em;
        border-radius: 10px;
    }

    .toAll {
        background:rgba(255,255,255, 0.9);
        padding: 0.5em;
        border-radius: 10px;
    }

    .whisper {
        background:rgba(200,200,200, 0.9);
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


</style>
