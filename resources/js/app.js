
import './bootstrap';
import { createApp } from 'vue';
const app = createApp({});
import ExampleComponent from './components/HomeComponent.vue';
app.component('example-component', ExampleComponent);
app.mount('#app');
