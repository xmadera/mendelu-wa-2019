<template>
  <div class="text-center text-background">
    <div class="text-left ml-5">
      <router-link :to="{ name: 'homepage' }">
        X
      </router-link>
    </div>
    <h1>Rooms</h1>
    <table class="table table-borderless">
      <tr v-for="room in rooms">
        <td>
          <router-link :to="{name: 'room', params: {id: room.id_rooms}}">
            {{room.title}}
          </router-link>
        </td>
        <td>
          <b-button type="submit" variant="danger" class="btn btn-sm">
            <font-awesome-icon icon="trash" />
          </b-button>
        </td>
      </tr>
    </table>
    <hr>
    <new-room></new-room>
    <!--<button v-on:click="loadRooms">Obnovit</button>-->
  </div>
</template>

<script>
import NewRoom from '@/views/NewRoom';

export default {
    components: {NewRoom},
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