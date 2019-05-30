<template>
  <div class="text-center text-background">
    <div class="text-left ml-5">
      <router-link :to="{ name: 'homepage' }">
        X
      </router-link>
    </div>
    <h1>Login</h1>

    <b-form @submit.prevent="doLogin" class="form-login text-left">
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
              label-for="pass">
      <b-form-input
              id="pass"
              v-model="password"
              type="password"
              required
      ></b-form-input>
      </b-form-group>



      <b-button type="submit" variant="primary" class="mt-2">
        <font-awesome-icon icon="sign-in-alt" />
        Log in
      </b-button>
    </b-form>

    <router-link :to="{ name: 'register' }">
      Register
    </router-link>
  </div>
</template>

<script>
  export default {
      data() {
          return  {
              login: '',
              password: ''
          }
      },
      methods: {
          doLogin() {
              this.$http.post('api/login',
                  // data posilane na server
                  {
                     login: this.login,
                     password: this.password
                  }
              ).then(
                  // uspesne prihlaseni
                  (response) => {
                      const token = response.data.token; // token prijaty ze serveru
                      console.log(token);
                      localStorage.setItem('token', token); // ulozeni tokenu do prohlizece naporad, pak je mozne ho nacitat pomoci localStorage.getItem('token') pri kazdem nacteni aplikace a nastavovat ho do axiosu viz dalsi radek
                      this.$http.defaults.headers.common['Authorization'] = response.data.token; // ulozeni docasne dovnitr axiosu, bude pouzito pro vsechny pozadavky na server, po obnoveni stranky zmizi
                      // presmerovani na jinou stranku po uspesnem loginu
                      if (this.$route.params.nextUrl !== undefined) {
                        this.$router.push({ path: this.$route.params.nextUrl });
                      } else {
                        this.$router.push({ name: 'rooms' });
                      }
                      window.location.reload();
                  }
              ).catch(
                  // neuspech
                  (error) => {
                    console.log(error);
                    alert("Check your credentials.");
                  }
              );
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

  .form-login {
    max-width: 50%;
    margin: auto;
  }

</style>
