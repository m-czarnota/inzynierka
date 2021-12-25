<template>
    <div class="board-component d-flex justify-content-center p-3">
        <div class="board"
             ref="boardRef">
            <div v-for="n in size" class="board-row">
                <field-component v-for="k in size"
                                 :data-coordinates="n + '' + k"
                                 :disable-props="disable"
                                 :is-user-owner="isUserOwner"
                                 @drop="dragDropShipHelper.onDrop($event)"
                                 @dragenter="dragDropShipHelper.onDragEnter($event)"
                                 @dragleave="dragDropShipHelper.onDragLeave($event)"
                                 @dragover.prevent></field-component>
            </div>
            <div v-if="isArrangeRoute"
                 class="board-buttons mt-2"
            >
                <button type="button" class="btn btn-light btn-outline-dark ms-2" @click="clear">Clear</button>
                <button type="button" class="btn btn-light btn-outline-dark ms-2" @click="drawAgain">Draw again</button>
            </div>
        </div>
    </div>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";
import {BoardField} from "../../entities/game/BoardField";
import {board} from "../../entities/game/Board";
import {shipPlacementService} from "../../services/ShipPlacementService";
import {emitter} from "../../services/Emitter";

export default {
    name: "BoardComponent",
    components: {FieldComponent},
    data() {
        return {
            size: 10,
            dragDropShipHelper: dragDropShipHelper,
        };
    },
    props: ['board', 'disable', 'isUserOwner'],
    mounted() {
        if (this.board.wasFirstMount) {
            this.updateBoard();
            return;
        }
        this.fillBoard();

        this.board.wasFirstMount = true;

        emitter.on('refreshBoard', isRefresh => {
            isRefresh ? this.$forceUpdate() : '';
        })
    },
    setup(props) {
        return {
            board: props.board ?? board,
        };
    },
    methods: {
        updateBoard() {
            this.$refs.boardRef.querySelectorAll('.board-row').forEach((row, i) => {
                row.querySelectorAll('.board-cell').forEach((fieldHtml, j) => {
                    fieldHtml.removeAttribute('data-coordinates');
                    this.board.fields[i][j].htmlElement = fieldHtml;
                });
            });

            this.board.ships.forEach(ship => {
                shipPlacementService.defineCustomEvent(null, ship, null);
                dragDropShipHelper.activeDragForPlacedShip(shipPlacementService.customEvent, ship);
            });
        },
        fillBoard() {
            this.$refs.boardRef.querySelectorAll('.board-row').forEach((row, index) => {
                this.board.fields.push([]);
                row.querySelectorAll('.board-cell').forEach(fieldHtml => {
                    const boardField = new BoardField();

                    boardField.calculateCoordinates(fieldHtml.getAttribute('data-coordinates'));
                    fieldHtml.removeAttribute('data-coordinates');
                    boardField.htmlElement = fieldHtml;

                    this.board.fields[index].push(boardField);
                });
            });
        },
        drawAgain() {
            shipPlacementService.putAllShipsToStorage();
            shipPlacementService.autoPlaceAllShips();
        },
        clear() {
            shipPlacementService.putAllShipsToStorage();
            emitter.emit('storage-rerender', true);
            dragDropShipHelper.setAppropriateColorForAllFields();
        },
    },
    computed: {
        isArrangeRoute() {
            return this.$router.currentRoute.value.name === 'Arrange Ships';
        }
    }
}
</script>

<style scoped>

</style>