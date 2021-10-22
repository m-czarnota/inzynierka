<template>
    <div class="board-cell" :class="{ disabled: disable }" ref="board-cell"></div>
</template>

<script>
import {gameState} from "../../services/GameState";
import {emitter} from "../../services/Emitter";
import {gameRouter} from "../../services/GameRouter";
import {requestStatuses, responseStatuses} from "../../loaders/appGame";
import {serveResponseRequestHelper} from "../../services/ServeResponseRequestHelper";

let id = 0

export default {
    name: "FieldComponent",
    data() {
        return {
            id: id++,
            disable: this.disableProps,
            lastShotCoordinates: null,
        };
    },
    props: ['disableProps', 'isUserOwner'],
    mounted() {
        if (this.disable !== undefined && !this.disable) {
            this.turnOn();
        }

        emitter.on('yourTurn', yourTurn => {
            if (this.isUserOwner) {
                return;
            }

            this.disable = !yourTurn;
        });
    },
    methods: {
        getBoard() {
            return this.$parent.$props.board;
        },
        turnOn() {
            this.$refs["board-cell"].addEventListener("click", this.shot);
        },
        turnOff() {
            this.$refs["board-cell"].removeEventListener("click", this.shot);
        },
        async shot(event) {
            const shotCoordinates = this.$parent.$props.board.getFieldByHtmlElement(event.target).coordinates;
            this.lastShotCoordinates = shotCoordinates;

            const formData = new FormData();
            formData.append('action', requestStatuses.shot);
            formData.append('coordinates', shotCoordinates);

            const response = await fetch(gameRouter.gameRoutes.servePlayerMove, {
                body: formData,
                method: 'POST'
            });
            const data = await response.json();

            this.actionAfterShot(data);
        },
        actionAfterShot(data) {
            switch (data.status) {
                case responseStatuses.error:
                    console.error(data.message);
                    break;
                case responseStatuses.hit:
                    serveResponseRequestHelper.serveHitRequest(data, this.getBoard(), this.lastShotCoordinates);
                    break;
                case responseStatuses.killed:
                    serveResponseRequestHelper.serveKillRequest(data, this.getBoard(), this.lastShotCoordinates);
                    break;
                case responseStatuses.miss_hit:
                    serveResponseRequestHelper.serveMissHitRequest(data);
                    gameState.changeTurn();
                    break;
                case responseStatuses.end_game:
                    serveResponseRequestHelper.serveEndGame(data);
                    break;
                case responseStatuses.walkover:
                    serveResponseRequestHelper.serveWalkover(data);
                    break;
            }
        },
    },
    watch: {
        disable(newVal) {
            newVal ? this.turnOff() : this.turnOn();
        },
    },
}
</script>

<style scoped>

</style>