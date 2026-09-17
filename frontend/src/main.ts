import { createApp } from "vue";
import { createPinia } from "pinia";
import "./assets/index.css";
import App from "./App.vue";
import { router } from "./routes";
import "./registerSW";

const pinia = createPinia();

const app = createApp(App);
app.config.globalProperties.$env = import.meta.env;
app.use(router).use(pinia).mount("#app");
