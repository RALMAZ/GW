// window._ = require('lodash');
// window.Popper = require('popper.js').default;

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


/**
 * Next, Vue application
 */
import Vue from 'vue';
import uikit from 'uikit';
import 'uikit/dist/css/uikit.min.css';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import Vuex from 'vuex';

Vue.use(Vuex);
Vue.use(ElementUI);

import RegisterGame from './components/RegisterGame.vue';


const store = new Vuex.Store({
  state: {
    token: 'tested'
  },
  mutations: {
    loadToken (loaded) {
      state.token = token;
    }
  }
});

const app = new Vue({
	store,
    el: '#app',
    components: {
    	RegisterGame
    }
});