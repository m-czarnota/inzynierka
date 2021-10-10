import KindOfGameComponent from "../components/game/KindOfGameComponent";
import ArrangeComponent from "../components/game/ArrangeComponent";
import GameComponent from "../components/game/GameComponent";
import NotFoundComponent from "../components/game/NotFoundComponent";
import {createRouter, createWebHistory} from "vue-router";
import {gameState} from "./GameState";

class GameRouter {
    constructor() {
        this.gameRoutes = {
            'prepareGame': this.getRoutePath('route-to-game-prepare-game'),
            'isUserInGame': this.getRoutePath('route-to-game-check-is-game'),
            'getUserShips': this.getRoutePath('route-to-game-get-user-ships'),
        }
        this.routeToGame = document.querySelector('#route-to-game').value;

        this.defineBasicRoutes();
        this.defineRouter();
        this.isUserInGame();
    }

    defineBasicRoutes() {
        this.routes = [
            {
                path: `${this.routeToGame}/`,
                name: 'Kind of Game',
                component: KindOfGameComponent,
                alias: `${this.routeToGame}/kind-of-game`,
            },
            {
                path: `${this.routeToGame}/arrange`,
                name: 'Arrange Ships',
                component: ArrangeComponent,
            },
            {
                path: `${this.routeToGame}/play`,
                name: 'Play',
                component: GameComponent,
            },
            {
                path: `${this.routeToGame}/:catchAll(.*)`,
                name: 'Not Found',
                component: NotFoundComponent,
            },
        ];
    }

    defineRouter() {
        const routes = this.routes;
        this.router = createRouter({
            history: createWebHistory(),
            routes,
        });
    }

    getRoutePath(id) {
        return document.querySelector(`#${id}`).value;
    }

    goToPlay(linkToRoom) {
        const gameRoomRouteName = 'Match';

        this.router.getRoutes().forEach(route => {
            if (!this.router.hasRoute(route.name) || route.name === gameRoomRouteName || route.name === 'Not Found') {
                return;
            }

            this.router.removeRoute(route.name);
        });

        this.router.addRoute({
            path: `${this.routeToGame}/${linkToRoom}`,
            name: gameRoomRouteName,
            component: GameComponent,
        });
        this.router.replace({name: gameRoomRouteName});

        gameState.isActiveGame = true;
    }

    async isUserInGame() {
        const response = await fetch(this.gameRoutes.isUserInGame);
        const data = await response.json();

        if (data.status) {
            localStorage.removeItem(gameState.gameInfoStorageKey);
            this.goToPlay(data.linkToRoom);
            return;
        }

        gameState.loadFromStorage();
    }
}

export const gameRouter = new GameRouter();