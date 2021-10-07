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
import {shipsStorage} from "../../entities/game/ShipsStorage";
import {gameState} from "../../services/GameState";
import {board} from "../../entities/game/Board";
import {shipPlacementService} from "../../services/ShipPlacementService";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";
import {gameRouter} from "../../services/GameRouter";
import {Ship} from "../../entities/game/Ship";

export default {
    name: "ArrangeComponent",
    components: {Board, ShipsStorageComponent},
    data() {
        return {
            whichApproach: 0,
            whichSecond: 0,
            maxSeconds: 39999,
            countSeconds: null,
        };
    },
    mounted() {
        shipPlacementService.autoPlaceAllShips();
        dragDropShipHelper.setAppropriateColorForAllFields();
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
            // document.querySelector('.play-button').setAttribute('disabled', 'true');
            this.prepareGameRequest();
        },
        prepareGameRequest() {
            this.whichApproach++;

            const formData = new FormData();
            formData.append('kindOfGame', gameState.kindOfGame);
            formData.append('playerShips', shipPlacementService.stringifyShips(board.ships));
            formData.append('whichApproach', this.whichApproach);

            const prepareGame = async () => {
                try {
                    const response = await fetch(gameRouter.gameRoutes.prepareGame, {
                        method: 'POST',
                        body: formData,
                        redirect: 'follow',
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        console.error(data.message);
                        return null;
                    }

                    if (response.status === 202) {
                        if (this.whichSecond > this.maxSeconds) {
                            this.gameNotFound(data.message);
                            return;
                        }

                        setTimeout(() => this.prepareGameRequest(), 500);
                        return;
                    }

                    return data;
                } catch (error) {
                    console.error(error);
                }

                return null;
            };

            (async () => {
                const data = await prepareGame();
                if (!data) {
                    return;
                }

                gameRouter.goToPlay(data.linkToRoom);
            })();
        },

        gameNotFound(message) {
            clearInterval(this.countSeconds);
            this.whichSecond = 0;
            this.whichApproach = 0;

            console.error(message);

            document.querySelector('.play-button').removeAttribute('disabled');
        },
    },
}
</script>

<style scoped>

</style>