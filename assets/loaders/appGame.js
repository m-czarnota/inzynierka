import {createApp} from "vue";
import {configureCompat} from "@vue/compat";
import {createWebHistory, createRouter} from "vue-router";

import ArrangeComponent from "../components/game/ArrangeComponent";
import GameComponent from "../components/game/GameComponent";
import MainComponent from "../components/game/MainComponent";
import NotFoundComponent from "../components/game/NotFoundComponent";
import KindOfGameComponent from "../components/game/KindOfGameComponent";

configureCompat({
    WATCH_ARRAY: true,
});

export const kindsOfGame = JSON.parse(window.atob(document.querySelector('#kinds-of-game').value));

const
    getRoutePath = id => document.querySelector(`#${id}`).value;
export const gameRoutes = {
    'prepareGame': getRoutePath('route-to-game-prepare-game')
}
export const routeToGame = document.querySelector('#route-to-game').value;

const routes = [
    {
        path: `${routeToGame}/`,
        name: 'Kind of Game',
        component: KindOfGameComponent,
        alias: `${routeToGame}/kind-of-game`,
    },
    {
        path: `${routeToGame}/arrange`,
        name: 'Arrange Ships',
        component: ArrangeComponent,
    },
    {
        path: `${routeToGame}/play`,
        name: 'Play',
        component: GameComponent,
    },
    {
        path: `${routeToGame}/:catchAll(.*)`,
        name: 'Not Found',
        component: NotFoundComponent,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
export default router;

const app = createApp(MainComponent);
app.use(router);
app.mount('#app');
