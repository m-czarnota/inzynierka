import {configureCompat} from "@vue/compat";

require('./controllers/GameState');

import {createApp} from "vue";
import BoardComponent from "./controllers/BoardComponent";
import ShipsStorageComponent from "./controllers/ShipsStorageComponent";
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
    components: {BoardComponent, ShipsStorageComponent},
    delimiters: ['${', '}$'],
});
app.config.globalProperties.emitter = emitter;
app.mount('#app');
