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
        };
    },
    props: ['disableProps', 'isUserOwner'],
    mounted() {
        this.$refs["board-cell"].classList.add(this.isUserOwner ? 'inactive' : 'active');
        if (this.disable !== undefined) {
            !this.disable ? this.turnOn() : this.$refs["board-cell"].classList.add('disabled');
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
            this.$refs["board-cell"].classList.remove('disabled');
            this.$refs["board-cell"].onclick = this.shot;
        },
        turnOff() {
            this.$refs["board-cell"].classList.add('disabled');
            this.$refs["board-cell"].onclick = null;
        },
        async shot(event) {
            const shotCoordinates = this.$parent.$props.board.getFieldByHtmlElement(event.target).coordinates;

            const formData = new FormData();
            formData.append('action', requestStatuses.shot);
            formData.append('coordinates', shotCoordinates);

            const response = await fetch(gameRouter.gameRoutes.servePlayerMove, {
                body: formData,
                method: 'POST'
            });
            const data = await response.json();

            serveResponseRequestHelper.serveAction(data, this.getBoard());
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