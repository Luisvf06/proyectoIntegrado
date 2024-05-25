import './assets/main.css'

import { VueElement, createApp } from 'vue'
import App from './App.vue'
import './assets/main.css';
import router from './router'

new VueElement({
    el: '#app',
    router,
    template: '<App/>',
    components: { App }
})
createApp(App).mount('#app')
