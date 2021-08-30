import {configureCompat} from "@vue/compat";

require('./controllers/GameState');

import {createApp} from "vue";
import Board from "./controllers/Board";
import ShipComponent from "./controllers/ShipComponent";
import ShipsStorage from "./controllers/ShipsStorage";
import {gameState} from "./controllers/GameState";
import {emitter} from "./controllers/Emitter";

configureCompat({
    WATCH_ARRAY: true,
});

const app = createApp({
    data() {
        return {
            gameState: gameState
        };
    },
    components: {Board, ShipsStorage},
    delimiters: ['${', '}$'],
});
app.config.globalProperties.emitter = emitter;
app.mount('#app');
