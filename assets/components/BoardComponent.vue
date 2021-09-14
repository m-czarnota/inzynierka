<template>
    <div class="board" ref="board">
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
import {dragDropShipHelper} from "../services/DragDropShipHelper";
import {BoardField} from "../entities/BoardField";
import {board} from "../entities/Board";

export default {
    name: "Board",
    components: {FieldComponent},
    data() {
        return {
            size: 10,
            dragDropShipHelper: dragDropShipHelper
        };
    },
    mounted() {
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
}
</script>

<style scoped>

</style>