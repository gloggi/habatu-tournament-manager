import { createApp } from "vue"
import App from "./App.vue"
import store from "./store"
import router from "./router"
import { mixin } from "./mixins.js"
import "./assets/tailwind.css"
import "./registerServiceWorker"

createApp(App).mixin(mixin).use(router).use(store).mount("#app")
