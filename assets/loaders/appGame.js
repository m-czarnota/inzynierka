import {createApp} from "vue";

import MainComponent from "../components/game/MainComponent";
import {gameRouter} from "../services/GameRouter";

export const kindsOfGame = JSON.parse(window.atob(document.querySelector('#kinds-of-game').value));
export const requestStatuses = JSON.parse(window.atob(document.querySelector('#request-statuses').value));
export const responseStatuses = JSON.parse(window.atob(document.querySelector('#response-statuses').value));

export const routeToDashboard = document.querySelector('#route-to-dashboard').value;

const app = createApp(MainComponent);
app.use(gameRouter.router);
app.mount('#app');
