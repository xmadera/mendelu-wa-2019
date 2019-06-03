// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import Axios from 'axios';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCoffee, faSignInAlt } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {faArrowRight} from "@fortawesome/free-solid-svg-icons/faArrowRight";
import {faPlusSquare} from "@fortawesome/free-solid-svg-icons/faPlusSquare";
import {faTrash} from "@fortawesome/free-solid-svg-icons/faTrash";
import {faUser} from "@fortawesome/free-solid-svg-icons/faUser";

// nastaveni knihovny pro synchronizaci dat
const axios = Axios.create();
axios.defaults.baseURL = 'https://akela.mendelu.cz/~xmadera/wa/backend/public/'; // base url for all server requests
Vue.prototype.$http = axios; // inject axios into all Vue components

// automaticke obnoveni prihlaseni, uzivatel je prihlasen na trvalo
const token = localStorage.getItem('token');
if (token !== null && token !== undefined) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = token; // bude pouzito pro vsechny pozadavky na server
}

// nastaveni knihovny na ikonky, inicializace konkretnich ikonek
library.add(faCoffee);
library.add(faSignInAlt);
library.add(faArrowRight);
library.add(faPlusSquare);
library.add(faTrash);
library.add(faUser)
Vue.component('font-awesome-icon', FontAwesomeIcon);


Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
});
