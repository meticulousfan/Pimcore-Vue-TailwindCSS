import { createApp } from 'vue'

import App from './Components/App.vue'
import axios from 'axios';
window.axios = axios;

import Toast from "vue-toastification";

createApp(App)
    .use(Toast, {})
    .mount('#app')
