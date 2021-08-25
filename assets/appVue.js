import {configureCompat} from "@vue/compat";

require('./controllers/GameState');

import {createApp} from "vue";
import Board from "./controllers/Board";
import ShipComponent from "./controllers/ShipComponent";
import {gameState} from "./controllers/GameState";

configureCompat({
    WATCH_ARRAY: true,
});

const app = createApp({
    data() {
        return {
            gameState: gameState
        };
    },
    components: {Board, ShipComponent},
    delimiters: ['${', '}$']
}).mount('#app');
