import { createApp } from "vue";
import Board from "./controllers/Board";
import ArrangeShips from "./controllers/ArrangeShips";

const app = createApp(ArrangeShips);
app.config.delimiters = ['${', '}$'];
app.mount('#app');
