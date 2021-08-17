import {configureCompat} from "@vue/compat";

require('./controllers/GameState');

import {createApp} from "vue";
import ArrangeShips from "./controllers/ArrangeShips";

configureCompat({
    WATCH_ARRAY: true,
});

const app = createApp(ArrangeShips);
app.config.delimiters = ['${', '}$'];
app.mount('#app');
