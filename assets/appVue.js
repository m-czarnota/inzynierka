import {configureCompat} from "@vue/compat";

require('./services/GameState');

import {createApp} from "vue";
import BoardComponent from "./components/BoardComponent";
import ShipsStorageComponent from "./components/ShipsStorageComponent";
import {gameState} from "./services/GameState";
import {emitter} from "./services/Emitter";

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
