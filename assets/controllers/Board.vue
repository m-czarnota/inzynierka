<template>
    <div class="board" ref="board">
        <div v-for="n in size" class="board-row">
            <field v-for="k in size" :coordinatesProp="n + '' + k"
                   @drop="onDrop($event)"
                   @dragenter="onDragEnter($event)"
                   @dragleave="onDragLeave($event)"
                   @dragover.prevent></field>
        </div>
    </div>
</template>

<script>
import Field from "./Field";
import {gameState} from "./GameState";

export default {
    name: "Board",
    components: {Field},
    data() {
        return {
            size: 10,
        };
    },
    mounted() {
        this.$refs.board.querySelectorAll('.board-row').forEach((row, index) => {
            gameState.boardArrangeFields.push([]);
            row.querySelectorAll('.board-cell').forEach(field => gameState.boardArrangeFields[index].push(field));
        });
    },
    methods: {
        onDrop(event) {
            console.log('jestem drop', event, 'to mój target:', event.target);
            let shipId = parseInt(event.dataTransfer.getData('ship'));
            let ship = gameState.shipsToDragging.find(shipToFind => shipToFind.id === shipId);
            // gameState.shipsToDragging.splice(gameState.shipsToDragging.indexOf(ship), 1);
            // TODO remove this ship from ships able to dragging after good drag

            let shipElements = JSON.parse(event.dataTransfer.getData('shipElements'));
            let shipSelectedElement = shipElements[parseInt(event.dataTransfer.getData('shipSelectedElement')) - 1];

            // allocate ships elements in suitable places
            let targetCoordinates = (() => {
                for (let i = 0; i < gameState.boardArrangeFields.length; i++) {
                    for (let j = 0; j < gameState.boardArrangeFields[i].length; j++) {
                        if (event.target === gameState.boardArrangeFields[i][j]) {
                            return {row: i, column: j};
                        }
                    }
                }
            })();

            let allOk = (() => {
                for (let i = 0; i < shipElements.length; i++) {
                    let shipElement = shipElements[i];
                    if (shipElement === shipSelectedElement) {
                        continue;
                    }

                    let checkedColumn = targetCoordinates.column - (shipSelectedElement.column - shipElement.column);
                    if (checkedColumn < 0 || checkedColumn >= gameState.boardArrangeFields.length) {
                        return false;
                    }

                    let checkedRow = targetCoordinates.row - (shipSelectedElement.row - shipElement.row);
                    if (checkedRow < 0 || checkedRow >= gameState.boardArrangeFields.length) {
                        return false;
                    }
                }

                return true;
            })();

            if (allOk) {
                shipElements.forEach((shipElement) => {
                    gameState.boardArrangeFields[targetCoordinates.row - (shipSelectedElement.row - shipElement.row)][targetCoordinates.column - (shipSelectedElement.column - shipElement.column)].style.backgroundColor = 'red';
                });
            }

            // TODO move above logic to separate functions
            // TODO use these new functions to service good onDragEnter and onDragLeave
        },
        onDragEnter(event) {
            event.target.style.backgroundColor = '#efefef';
            console.log('robie drag enter');
        },
        onDragLeave(event) {
            event.target.style.backgroundColor = "";
            console.log('robie drag leave');
        },
    }
}
</script>

<style scoped>

</style>