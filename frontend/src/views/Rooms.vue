<template>
  <div class="text-center text-background">
    <b-row align-h="between">
      <b-col cols="2">
        <router-link :to="{ name: 'homepage' }">
          X
        </router-link>
      </b-col>
      <b-col cols="3">
        <router-link :to="{ name: 'newroom' }">
          Create room <font-awesome-icon icon="plus-square"/>
        </router-link>
      </b-col>
    </b-row>
    <h1>Rooms</h1>
    <hr>
    <table class="table table-borderless">
      <tr v-for="room in rooms">
        <td>
          <router-link :to="{name: 'room', params: {id: room.id_rooms, title: room.title}}">
            {{room.title}}
          </router-link>
        </td>
      </tr>
    </table>
    <hr>
  </div>
</template>

<script>

export default {
    data() {
        return {
            rooms: []
        }
    },
    mounted() {
        this.loadRooms();
    },
    methods: {
        loadRooms() {
            this.$http.get('api/auth/rooms')
                .then(response => {
                    this.rooms = response.data;
                });
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

  .table {
    max-width: 80%;
    margin: auto;
  }

</style>