<template>
    <div class="arrange-component col-12">
        <main class="arrange-main col-12 d-flex flex-wrap-reverse">
            <ships-storage-component class="col-12 col-md-6"></ships-storage-component>
            <board-component class="col-12 col-md-6"></board-component>
        </main>
        <div class="buttons d-flex justify-content-around mt-5">
            <button type="button" class="btn btn-secondary back-button p-3 fw-bold" @click="back">Back</button>
            <button type="button" class="btn btn-success play-button p-3 fw-bold" @click="play">Play</button>
        </div>
    </div>
</template>

<script>
import ShipsStorageComponent from "./ShipsStorageComponent";
import BoardComponent from "./BoardComponent";
import {shipsStorage} from "../../entities/game/ShipsStorage";
import {gameState} from "../../services/GameState";
import {board} from "../../entities/game/Board";
import {shipPlacementService} from "../../services/ShipPlacementService";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";
import {gameRouter} from "../../services/GameRouter";
import {kindsOfGame} from "../../loaders/appGame";
import {emitter} from "../../services/Emitter";
import {timeUtil} from "../../utils/TimeUtil";

export default {
    name: "ArrangeComponent",
    components: {BoardComponent, ShipsStorageComponent},
    data() {
        return {
            whichApproach: 0,
            whichSecond: 0,
            maxSeconds: 39999,
            countSeconds: null,
        };
    },
    mounted() {
        if (board.ships.length === 0) {
            shipPlacementService.autoPlaceAllShips();
            dragDropShipHelper.setAppropriateColorForAllFields();
        }
    },
    methods: {
        back() {
            this.$router.replace({name: 'Kind of Game'});
        },
        play() {
            if (shipsStorage.ships.length !== 0) {
                this.displayError('You must place all ships on the board!');
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
            formData.append('playerShips', shipPlacementService.stringifyShips(board.ships));
            formData.append('whichApproach', this.whichApproach);

            if ([kindsOfGame.game_ai, kindsOfGame.game_ai_ranked].includes(gameState.kindOfGame)) {
                shipPlacementService.putAllShipsToStorage();
                shipPlacementService.autoPlaceAllShips();
                formData.append('aiShips', shipPlacementService.stringifyShips(board.ships));
            }

            const prepareGame = async () => {
                try {
                    const response = await fetch(gameRouter.gameRoutes.prepareGame, {
                        method: 'POST',
                        body: formData,
                        redirect: 'follow',
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.displayError(data.message);
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
                    this.displayError(error);
                }

                return null;
            };

            (async () => {
                const data = await prepareGame();
                if (!data) {
                    return;
                }

                gameRouter.goToPlay(data.linkToRoom);

                shipsStorage.remove();
                board.remove();
            })();
        },
        gameNotFound(message) {
            clearInterval(this.countSeconds);
            this.whichSecond = 0;
            this.whichApproach = 0;

            this.displayError(message);

            document.querySelector('.play-button').removeAttribute('disabled');
        },
        displayError(message) {
            emitter.emit('newBasicToast', {
                header: 'Error',
                message: message,
                time: timeUtil.getCurrentTimeWithoutSeconds(),
            });
        }
    },
}
</script>

<style scoped>

</style>