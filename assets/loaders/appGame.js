import {createApp} from "vue";
import {configureCompat} from "@vue/compat";
import {createWebHistory, createRouter} from "vue-router";

require('../services/GameState');

import {gameState} from "../services/GameState";
import {emitter} from "../services/Emitter";
import ArrangeComponent from "../components/game/ArrangeComponent";
import GameComponent from "../components/game/GameComponent";
import MainComponent from "../components/game/MainComponent";
import NotFoundComponent from "../components/game/NotFoundComponent";

configureCompat({
    WATCH_ARRAY: true,
});

const routeToGame = document.querySelector('#route-to-game').value;

const routes = [
    {
        path: `${routeToGame}/`,
        name: 'Arrange Ships',
        component: ArrangeComponent,
        alias: `${routeToGame}/arrange`,
    },
    {
        path: `${routeToGame}/play`,
        name: 'Game',
        component: GameComponent,
    },
    {
        path: `${routeToGame}/:catchAll(.*)`,
        component: NotFoundComponent,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
export default router;

// const app = createApp({
//     data() {
//         return {
//             gameState: gameState
//         };
//     },
//     components: {ArrangeComponent},
//     delimiters: ['${', '}$'],
// });
// app.config.globalProperties.emitter = emitter;
const app = createApp(MainComponent);
app.use(router);
app.mount('#app');
