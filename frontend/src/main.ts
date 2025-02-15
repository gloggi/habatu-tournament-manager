import { createApp } from "vue";
import { createPinia } from "pinia";
import "./assets/index.css";
import App from "./App.vue";
import { router } from "./routes";
import "./registerSW";

const pinia = createPinia();

createApp(App).use(router).use(pinia).mount("#app");
