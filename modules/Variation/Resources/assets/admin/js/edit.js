import { createApp } from "vue";
import { trans } from "@admin/js/functions";
import App from "./edit/App.vue";

const app = createApp(App);

app.config.globalProperties.trans = trans;

app.mount("#app");
