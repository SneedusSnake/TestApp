import './bootstrap';
import '../scss/styles.scss'
import * as bootstrap from 'bootstrap'
import { createApp } from 'vue'
import i18n from "./i18n";
import App from './src/Components/App.vue'

console.log(i18n)

const app = createApp(App).use(i18n).mount('#app');
