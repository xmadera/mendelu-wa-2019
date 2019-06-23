<template>
    <div class="text-center text-background">
        <b-row align-h="between">
            <b-col cols="2">
                <router-link :to="{ name: 'homepage' }">
                    X
                </router-link>
            </b-col>
        </b-row>
        <h3>{{userData.login}}</h3>
        <b-form @submit.prevent="editUser" class="form-create text-left">
        <b-row align-h="center" class="mt-5">
            <b-col cols="5">
                    <b-form-group
                            label="Name:"
                            label-for="name">
                        <b-form-input
                                id="name"
                                v-model="userData.name"
                                type="text"
                                required
                        ></b-form-input>
                    </b-form-group>
            </b-col>
            <b-col cols="5">
                <b-form-group
                        label="Surname:"
                        label-for="surname">
                    <b-form-input
                            id="surname"
                            v-model="userData.surname"
                            type="text"
                            required
                    ></b-form-input>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row align-h="center" class="mt-2">
            <b-col cols="5">
                <b-form-group
                        label="Email:"
                        label-for="email">
                    <b-form-input
                            id="email"
                            v-model="userData.email"
                            type="text"
                            required
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <b-col cols="5">
                <b-form-group
                        label="Gender:"
                        label-for="gender">
                    <b-form-select
                            id="gender"
                            v-model="gender"
                            :options="Genders"
                            type="text"
                            required
                    ></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>
            <div class="text-center">
                <b-button type="submit" variant="outline-primary" v-on:click="updateUser">
                    <font-awesome-icon icon="plus-square" />
                    Edit
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        data() {
            return {
                userData: '',
                gender: null,
                Genders: [{ text: 'Select One', value: null },"male", "female"]
            }
        },

        mounted() {
            this.user();
        },

        methods: {
            user: function () {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.userData = response.data;
                    })
            },

            updateUser: function() {
                this.$http.put('/api/auth/updateUser', {
                    userLogin: this.userData.login,
                    name: this.userData.name,
                    surname: this.userData.surname,
                    email: this.userData.email,
                    gender: this.gender
                }).then(() => { window.location.reload();
                    }
                )
            }
        }
    }
</script>

<style scoped>
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