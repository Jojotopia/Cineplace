import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index.js'
import './assets/css/reset.css'
import './assets/css/style.css'
import './assets/css/responsive.css'
import './assets/js/script.js'
import logo from './assets/img/mini-icone.svg'

const app = createApp(App)

app.use(router)
app.use(createPinia())

app.mount('#app')
