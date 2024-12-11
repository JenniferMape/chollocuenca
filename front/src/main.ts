import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { plugin, defaultConfig } from '@formkit/vue'
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import App from './App.vue';
import router from './router';
import config from '@/formkit.config';


const app = createApp(App);

const options = {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
};

app.use(Toast, options);

app.use(plugin, defaultConfig(config))
app.use(createPinia());
app.use(router);

app.mount('#app');
