<template>
    <div>
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

</style>