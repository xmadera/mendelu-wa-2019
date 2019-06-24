<template>
    <div class="text-center text-background">
    <h1>Chat</h1>
    <router-link v-if="isLoggedIn" :to="{ name: 'rooms' }">
      Rooms
    </router-link>
    <template v-if="!isLoggedIn">
      Welcome, to gain access to our chatting rooms, please register
    </template>
    <router-link v-if="!isLoggedIn" :to="{ name: 'register' }">
      here.
    </router-link>
    </div>
</template>

<script>
  export default {
    computed: {
      isLoggedIn: function () {
        const token = localStorage.getItem('token');
        return token !== null && token !== undefined;
      }
    }
  }
</script>

<style scoped>

  .text-background {
      background:rgba(255,255,255, 0.6);
    margin-top: 7em;
      margin-bottom: 7em;
      padding-top: 3em;
    padding-bottom: 3em;
    border-radius: 6px;
    font-weight: bold;
  }

</style>
