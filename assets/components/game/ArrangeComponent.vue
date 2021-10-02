<template>
    <div class="arrange-component">
        <main class="arrange-main">
            <ships-storage-component></ships-storage-component>
            <board></board>
        </main>
        <div class="buttons d-flex justify-content-around mt-5">
            <button type="button" class="btn btn-secondary back-button" @click="back">Back</button>
            <button type="button" class="btn btn-warning play-button" @click="play">Play</button>
        </div>
    </div>
</template>

<script>
import ShipsStorageComponent from "./ShipsStorageComponent";
import Board from "./BoardComponent";
import {gameRoutes, routeToGame} from "../../loaders/appGame";
import {shipsStorage} from "../../entities/game/ShipsStorage";
import GameComponent from "./GameComponent";
import {gameState} from "../../services/GameState";
import {board} from "../../entities/game/Board";
import {shipPlacementService} from "../../services/ShipPlacementService";

export default {
    name: "ArrangeComponent",
    components: {Board, ShipsStorageComponent},
    data() {
        return {
            whichApproach: 0,
            whichSecond: 0,
            maxSeconds: 3,
            countSeconds: null,
        };
    },
    mounted() {
        shipPlacementService.autoPlaceAllShips();
    },
    methods: {
        back() {
            this.$router.replace({name: 'Kind of Game'});
        },
        play() {
            if (shipsStorage.ships.length !== 0) {
                console.error('You must place all ships on the board!');
                return;
            }

            this.countSeconds = setInterval(() => this.whichSecond++, 1000);
            document.querySelector('.play-button').setAttribute('disabled', 'true');
            this.prepareGameRequest();
        },
        prepareGameRequest() {
            this.whichApproach++;

            const formData = new FormData();
            formData.append('kindOfGame', gameState.kindOfGame);
            formData.append('playerShips', JSON.stringify(board.ships, board.mapShipBeforeSaving));
            formData.append('whichApproach', this.whichApproach);

            const prepareGame = async () => {
                try {
                    const response = await fetch(gameRoutes.prepareGame, {
                        method: 'POST',
                        body: formData,
                    });

                    console.log(response);
                    if (!response.ok) {
                        console.error('moj status jest false', response);
                        return null;
                    }

                    console.log('przemieniam', response.message);
                    const data = await response.json();
                    console.log('data', data);

                    if (response.status === 204) {
                        console.log('nie znaleziono, message:', data.message);
                        if (this.whichSecond > this.maxSeconds) {
                            this.gameNotFound(data.message);
                            return;
                        }

                        this.prepareGameRequest();
                        return;
                    }

                    return data;
                } catch (error) {
                    console.error(error);
                    console.error(error.name, error.message);
                }

                return null;
            };

            (async () => {
                const data = await prepareGame();
                if (!data) {
                    return;
                }

                this.goToPlay(data.linkToRoom);
            })();
        },
        goToPlay(linkToRoom) {
            const gameRoomRouteName = 'Match';

            this.$router.getRoutes().forEach(route => {
                if (!this.$router.hasRoute(route.name) || route.name === gameRoomRouteName || route.name === 'Not Found') {
                    return;
                }

                this.$router.removeRoute(route.name);
            });

            this.$router.addRoute({
                path: `${routeToGame}/${linkToRoom}`,
                name: gameRoomRouteName,
                component: GameComponent,
            });
            this.$router.replace({name: gameRoomRouteName});

            gameState.isActiveGame = true;
        },
        gameNotFound(message) {
            clearInterval(this.countSeconds);
            this.whichSecond = 0;
            this.whichApproach = 0;
            console.error(message);

            document.querySelector('.play-button').setAttribute('disabled', 'false');
        },
    },
}
</script>

<style scoped>

</style>