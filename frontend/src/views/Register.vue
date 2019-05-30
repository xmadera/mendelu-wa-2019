<template>
  <div class="text-center text-background">
    <div class="text-left ml-5">
    <router-link :to="{ name: 'homepage' }">
      X
    </router-link>
    </div>
    <h1>Registration</h1>

    <b-form @submit.prevent="register" class="form-register text-left">
      <b-form-group
              label="Login:"
              label-for="login">
      <b-form-input
              id="login"
              v-model="login"
              type="text"
              required
      ></b-form-input>
      </b-form-group>

      <b-form-group
              label="Password:"
              label-for="pass1">
      <b-form-input
              id="pass1"
              v-model="password1"
              type="password"
              required
      ></b-form-input>
      </b-form-group>


      <b-form-group
              label="Password check:"
              label-for="pass2">
      <b-form-input
              id="pass2"
              v-model="password2"
              type="password"
              required
      ></b-form-input>
      </b-form-group>


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


      <b-form-group
              label="Surname:"
              label-for="surname">
      <b-form-input
              id="surname"
              v-model="surname"
              type="text"
              required
      ></b-form-input>
      </b-form-group>


      <b-form-group
              label="Email:"
              label-for="email">
      <b-form-input
              id="email"
              v-model="email"
              type="email"
              required
      ></b-form-input>
      </b-form-group>


      <b-form-group
              label="Gender:"
              label-for="gender">
        <b-form-select
                id="gender"
                v-model="gender"
                required>
                <option value="male">Male</option>
                <option value="female">Female</option>
        </b-form-select>
      </b-form-group>



      <b-button type="submit" variant="primary" class="mt-2">
        <font-awesome-icon icon="arrow-right" />
        Register
      </b-button>
    </b-form>

  </div>
</template>

<script>
  export default {
    //jako data: function() {}
    data() {
      return {
        name: '',
        surname: '',
        email: '',
        password1: '',
        password2: '',
        gender: 'male',
        login: ''
      };
    },
    methods: {
      register() {
        if(this.password1 != this.password2) {
          alert('Hesla se neshoduji.');
          return;
        }
        this.$http.post('api/register', {
          name: this.name,
          surname: this.surname,
          login: this.login,
          password: this.password1,
          gender: this.gender,
          email: this.email
        }).then(() => {
          this.$router.push({ name: 'login' });
        }, () => {
          alert('Chyba registrace');
        });
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

  .form-register {
    max-width: 50%;
    margin: auto;
  }

</style>