import {createApp} from 'vue'
import store from './store.js'
import router from "./router.js";
import App from "./App.vue";
import Vue3EasyDataTable from 'vue3-easy-data-table';
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput'

import 'vue3-easy-data-table/dist/style.css';
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import "./styles/global.css"
import 'maz-ui/css/main.css' // Make sure to include the CSS styles

const app = createApp(App);
app.use(router);
app.use(store);
app.component('EasyDataTable', Vue3EasyDataTable);
app.component('MazPhoneNumberInput', MazPhoneNumberInput)

app.mount('#app');
