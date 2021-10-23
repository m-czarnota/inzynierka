<template>
    <div class="board-cell" ref="board-cell"></div>
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
        this.$refs["board-cell"].classList.add(this.isUserOwner ? 'inactive' : 'active');
        this.disable !== undefined && !this.disable ? this.turnOn() : this.$refs["board-cell"].classList.add('disabled');

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
            this.$refs["board-cell"].classList.remove('disabled');
            this.$refs["board-cell"].addEventListener("click", this.shot);
        },
        turnOff() {
            this.$refs["board-cell"].classList.add('disabled');
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
            console.log(data.message);
            switch (data.status) {
                case responseStatuses.error:
                    console.error(data.message);
                    break;
                case responseStatuses.hit:
                    this.serveHit();
                    break;
                case responseStatuses.killed:
                    this.serveKill();
                    break;
                case responseStatuses.miss_hit:
                    this.serveMissHit();
                    break;
                case responseStatuses.end_game:
                    this.actionAfterShot(data.originalAction);
                    serveResponseRequestHelper.serveEndGame(data);
                    break;
                case responseStatuses.walkover:
                    serveResponseRequestHelper.serveWalkover(data);
                    break;
            }
        },
        serveHit() {
            const field = this.getBoard().getFieldByCoordinates(this.lastShotCoordinates);
            field.setHitStatus();
        },
        serveKill() {
            const field = this.getBoard().getFieldByCoordinates(this.lastShotCoordinates);
            const ship = this.getBoard().ships.find(ship => ship === field.shipPointer);
            ship.setKilledStatus();
        },
        serveMissHit() {
            const field = this.getBoard().getFieldByCoordinates(this.lastShotCoordinates);
            field.setMisHitStatus();

            setTimeout(() => gameState.changeTurn(), 1000);
        }
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