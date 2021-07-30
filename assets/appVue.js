require('./controllers/GameState');

import {createApp} from "vue";
import ArrangeShips from "./controllers/ArrangeShips";

const app = createApp(ArrangeShips);
app.config.delimiters = ['${', '}$'];
app.mount('#app');
