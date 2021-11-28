
require("./bootstrap");
require("./custom");

// Imports External
import Vue from 'vue';
import Vuelidate from 'vuelidate';
import VueFormWizard from 'vue-form-wizard';

// Imports Components
import SendToken from './components/SendToken/SendToken.vue';
import SendTokenButton from "./components/SendToken/SendTokenButton";

Vue.use(Vuelidate);
Vue.use(VueFormWizard);

Vue.prototype.$http = window.axios;
Vue.prototype.$http.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json';

Vue.component('send-token', SendToken);
Vue.component('send-token-button', SendTokenButton);

const app = new Vue({
    el: '#header',
    methods: {

    }
});



