<template>
    <div class="board" ref="board">
        <div v-for="n in size" class="board-row">
            <field-component v-for="k in size" :data-coordinates="n + '' + k"
                             @drop="onDrop($event)"
                             @dragenter="onDragEnter($event)"
                             @dragleave="onDragLeave($event)"
                             @dragover.prevent></field-component>
        </div>
    </div>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {gameState} from "./GameState";
import {BoardField} from "./BoardField";

export default {
    name: "Board",
    components: {FieldComponent},
    data() {
        return {
            size: 10,
        };
    },
    mounted() {
        this.$refs.board.querySelectorAll('.board-row').forEach((row, index) => {
            gameState.boardArrangeFields.push([]);
            row.querySelectorAll('.board-cell').forEach(fieldHtml => {
                let boardField = new BoardField();
                boardField.calculateCoordinates(fieldHtml.getAttribute('data-coordinates'));
                fieldHtml.removeAttribute('data-coordinates');
                boardField.htmlElement = fieldHtml;

                gameState.boardArrangeFields[index].push(boardField);
            });
        });
    },
    methods: {
        onDrop(event) {
            if (!this.canPlaceShipOnBoardField(event.target)) {
                this.setAppropriateColorForAllFields();
                return;
            }

            let shipId = parseInt(event.dataTransfer.getData('ship'));
            let ship = gameState.shipsToDragging.find(shipToFind => shipToFind.id === shipId);
            // gameState.shipsToDragging.splice(gameState.shipsToDragging.indexOf(ship), 1);
            // TODO remove this ship from ships able to dragging after good drag

            // allocate ships elements in suitable places
            let shipMovingProperty = this.targetsInBoardBoundary(event);
            if (!shipMovingProperty) {
                return;
            }

            shipMovingProperty.shipElements.forEach((shipElement, index) => {
                let row = shipMovingProperty.targetCoordinates.row - (shipMovingProperty.shipSelectedElement.row - shipElement.row);
                let column = shipMovingProperty.targetCoordinates.column - (shipMovingProperty.shipSelectedElement.column - shipElement.column);
                let shipField = gameState.boardArrangeFields[row][column];

                // base operations
                shipField.blockField();
                shipField.shipPointer = ship;
                shipField.numberOfShipElement = index + 1;
                shipField.htmlElement.style.backgroundColor = 'red';
                ship.boardFields.push(shipField);

                // block around fields
                let coordinatesToAroundFields = this.getCoordinatesToAroundFields(row, column);
                coordinatesToAroundFields.forEach(coordinates => {
                    if (!gameState.checkIfCoordinatesAreInBoardBoundary(coordinates.row, coordinates.column)) {
                        return;
                    }

                    let fieldAroundShip = gameState.boardArrangeFields[coordinates.row][coordinates.column];
                    if (!fieldAroundShip.shipPointer && !fieldAroundShip.isNextToShipPointers.find(item => item === ship)) {  // field does not belong to other ship
                        fieldAroundShip.blockField(ship);
                        ship.aroundFields.push(fieldAroundShip);
                    }

                    // TODO allow drag placed ship from his place out of board
                    // TODO remember ship's last position on board
                });
            });

            ship.boardFields.forEach(field => {
                field.htmlElement.setAttribute('draggable', 'true');
                field.htmlElement.ondragstart = (event) => {
                    event.dataTransfer.setData('ship', ship.id);
                    event.dataTransfer.setData('shipSelectedElement', field.numberOfShipElement);
                    event.dataTransfer.setData('shipElements', JSON.stringify(ship.elementsGridProperties));

                    ship.boardFields.forEach(field => field.unblockField(ship));
                    ship.boardFields = [];
                    ship.aroundFields.forEach(field => field.unblockField(ship));
                    ship.aroundFields = [];
                };
            });
        },
        onDragEnter(event) {
            // TODO change cursor on blocked when is cannot place the ship
            this.changeColorFieldForDraggedShip(event);
        },
        onDragLeave(event) {
            this.changeColorFieldForDraggedShip(event, false);
        },
        setAppropriateColorForAllFields() {
            // TODO better colors
            for (let field of gameState.boardArrangeFields.flat()) {
                if (field.shipPointer) {
                    field.htmlElement.style.backgroundColor = 'red';
                } else if (field.isNextToShipPointers.length) {
                    field.htmlElement.style.backgroundColor = 'grey';
                } else {
                    field.htmlElement.style.backgroundColor = 'hsl(140, 25%, 68%)';
                }
            }
        },
        canPlaceShipOnBoardField(htmlElement) {
            for (let [index, field] of gameState.boardArrangeFields.flat().entries()) {
                if (htmlElement === field.htmlElement) {
                    let fieldPosition = gameState.calculateFieldPositionFromIndex(index);
                    let coordinatesToAroundFields = this.getCoordinatesToAroundFields(fieldPosition.row, fieldPosition.column);

                    for (let coordinatesToAroundField of coordinatesToAroundFields) {
                        if (!gameState.checkIfCoordinatesAreInBoardBoundary(coordinatesToAroundField.row, coordinatesToAroundField.column)) {
                            continue;
                        }

                        field = gameState.boardArrangeFields[coordinatesToAroundField.row][coordinatesToAroundField.column];
                        if (!field.isActive) {
                            return false;
                        }
                    }
                }
            }

            return true;
        },
        getCoordinatesToAroundFields(row, column) {
            return [
                {row: row - 1, column: column},
                {row: row + 1, column: column},
                {row: row, column: column - 1},
                {row: row, column: column + 1},
                {row: row - 1, column: column - 1},
                {row: row - 1, column: column + 1},
                {row: row + 1, column: column - 1},
                {row: row + 1, column: column + 1},
            ]
        },
        changeColorFieldForDraggedShip(event, addColor = true, color = 'blue') {
            let shipMovingProperty = this.targetsInBoardBoundary(event);
            if (shipMovingProperty) {
                shipMovingProperty.shipElements.forEach((shipElement) => {
                    let row = shipMovingProperty.targetCoordinates.row - (shipMovingProperty.shipSelectedElement.row - shipElement.row);
                    let column = shipMovingProperty.targetCoordinates.column - (shipMovingProperty.shipSelectedElement.column - shipElement.column);

                    if (gameState.boardArrangeFields[row][column].isActive) {
                        gameState.boardArrangeFields[row][column].htmlElement.style.backgroundColor = addColor ? color : '';
                    }
                });
            }
        },
        targetsInBoardBoundary(event) {
            let shipElements = JSON.parse(event.dataTransfer.getData('shipElements'));
            let numberOfShipSelectedElement = parseInt(event.dataTransfer.getData('shipSelectedElement'));
            let shipSelectedElement = shipElements[numberOfShipSelectedElement - 1];

            let targetCoordinates = (() => {
                for (let [index, field] of gameState.boardArrangeFields.flat().entries()) {
                    if (event.target === field.htmlElement) {
                        return gameState.calculateFieldPositionFromIndex(index);
                    }
                }
            })();

            for (let shipElementGridValues of shipElements) {
                let checkedRow = targetCoordinates.row - (shipSelectedElement.row - shipElementGridValues.row);
                let checkedColumn = targetCoordinates.column - (shipSelectedElement.column - shipElementGridValues.column);

                if (!gameState.checkIfCoordinatesAreInBoardBoundary(checkedRow, checkedColumn)) {
                    return null;
                }
            }

            return {
                targetCoordinates: targetCoordinates,
                shipElements: shipElements,
                shipSelectedElement: shipSelectedElement
            };
        },
    }
}
</script>

<style scoped>

</style>