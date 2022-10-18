import { createApp } from "vue"
import App from "./App.vue"
import store from "./store"
import router from "./router"
import { mixin } from "./mixins.js"
import "./assets/tailwind.css"
router.beforeEach((to, from, next) => {
	if (to.name !== "login" && !localStorage.token) next({ name: "login" })
	else next()
})
router.beforeEach((to, from, next) => {
	if (to.name == "login" && localStorage.token) next({ name: "home" })
	else next()
})

createApp(App).use(router).use(store).mixin(mixin).mount("#app")
