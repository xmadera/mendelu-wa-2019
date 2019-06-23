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
            <div v-if="isKicked(room.id_rooms)">
              {{room.title}}(You have been kicked)
          </div>
          <div v-else-if="room.lock == true && room.id_users_owner != userData.id_users">
            {{room.title}} (locked)
          </div>
          <div v-else>
            <router-link :to="{name: 'room', params: {id: room.id_rooms}}" v-on:click.native="saveUserInRoom(room.id_rooms)">
              {{room.title}}
            </router-link>
          </div>
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
            rooms: [],
          userData: '',
          kickData: [],
        }
    },
    mounted() {
      this.user();
      this.Kicks();
      this.loadRooms();

      this.reloader = setInterval(() => {
        this.Kicks();
        this.loadRooms();
      }, 2000); // zavola metodu pro stazeni dat
    },
  // pred zrusenim komponenty
  beforeDestroy() {
    clearInterval(this.reloader);
  },

    methods: {
        loadRooms() {
            this.$http.get('api/auth/rooms')
                .then(response => {
                    this.rooms = response.data;
                });
        },
      saveUserInRoom(id) {
        this.$http.post('/api/auth/saveUserInRoom', {
          roomId: id
        }).then(() => {
                }
        ).catch(() => {
        })
      },

      Kicks() {
        this.$http.get('api/auth/kicks', {
        })
                .then(response => {
                  this.kickData = response.data;
                })
      },

      user() {
        this.$http.get('api/auth/user')
                .then(response => {
                  this.userData = response.data;
                })
      },
      isKicked(id) {
        for (let i = 0; i < this.kickData.length; i++) {
          if (this.kickData[i].id_rooms == id && this.kickData[i].id_users == this.userData.id_users) {
              return true
          }
        }
        return false;
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