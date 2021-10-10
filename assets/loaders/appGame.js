import {createApp} from "vue";
import {configureCompat} from "@vue/compat";

import MainComponent from "../components/game/MainComponent";
import {gameRouter} from "../services/GameRouter";

export const kindsOfGame = JSON.parse(window.atob(document.querySelector('#kinds-of-game').value));

const app = createApp(MainComponent);
app.use(gameRouter.router);
app.mount('#app');
