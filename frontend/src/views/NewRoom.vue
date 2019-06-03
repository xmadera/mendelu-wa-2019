<template>
    <div class="text-center text-background">
        <h3>Create rooms</h3>
        <b-form @submit.prevent="createRoom" class="form-create text-left">
            <b-form-group
                    label="Name:"
                    label-for="name">
                <b-form-input
                        id="name"
                        v-model="name"
                        type="text"
                        required
                ></b-form-input>
            </b-form-group>

            <b-button type="submit" variant="primary">
                <font-awesome-icon icon="plus-square" />
                Create room
            </b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        name: "NewRoom",
        data() {
            return {
                name: ""
            }
        },
        methods: {
            createRoom() {
                this.$http.post('/api/auth/rooms', {
                    title: this.name
                }).then(() => {
                    this.name = "";
                    alert("Mistnost vytvorena");
                    this.$router.push({name: 'rooms'});
                }).catch(() => {
                    alert("Chyba");
                })
            }

        }
    }
</script>

<style scoped>

    .form-create {
        max-width: 50%;
        margin: auto;
    }

    .text-background {
        margin-top: 7em;
        margin-bottom: 7em;
        padding-top: 2em;
        padding-bottom: 3em;
        border-radius: 6px;
        font-weight: bold;
        background:rgba(255,255,255, 0.6);
    }

</style>