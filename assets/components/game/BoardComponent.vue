<template>
    <div class="board"
         ref="board"
         :class="{ 'col-6': $router.currentRoute.value.name === 'Arrange Ships' }">
        <div v-for="n in size" class="board-row">
            <field-component v-for="k in size" :data-coordinates="n + '' + k"
                             @drop="dragDropShipHelper.onDrop($event)"
                             @dragenter="dragDropShipHelper.onDragEnter($event)"
                             @dragleave="dragDropShipHelper.onDragLeave($event)"
                             @dragover.prevent></field-component>
        </div>
    </div>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";
import {BoardField} from "../../entities/game/BoardField";
import {board} from "../../entities/game/Board";
import {shipPlacementService} from "../../services/ShipPlacementService";

export default {
    name: "BoardComponent",
    components: {FieldComponent},
    data() {
        return {
            size: 10,
            dragDropShipHelper: dragDropShipHelper,
            board: board,
        };
    },
    mounted() {
        if (board.wasFirstMount) {
            this.updateBoard();
            return;
        }
        this.fillBoard();

        board.wasFirstMount = true;
    },
    methods: {
        updateBoard() {
            this.$refs.board.querySelectorAll('.board-row').forEach((row, i) => {
                row.querySelectorAll('.board-cell').forEach((fieldHtml, j) => {
                    fieldHtml.removeAttribute('data-coordinates');
                    board.fields[i][j].htmlElement = fieldHtml;
                });
            });

            board.ships.forEach(ship => {
                shipPlacementService.defineCustomEvent(null, ship, null);
                dragDropShipHelper.activeDragForPlacedShip(shipPlacementService.customEvent, ship);
            });
        },
        fillBoard() {
            this.$refs.board.querySelectorAll('.board-row').forEach((row, index) => {
                board.fields.push([]);
                row.querySelectorAll('.board-cell').forEach(fieldHtml => {
                    const boardField = new BoardField();

                    boardField.calculateCoordinates(fieldHtml.getAttribute('data-coordinates'));
                    fieldHtml.removeAttribute('data-coordinates');
                    boardField.htmlElement = fieldHtml;

                    board.fields[index].push(boardField);
                });
            });
        },
    },
}
</script>

<style scoped>

</style>