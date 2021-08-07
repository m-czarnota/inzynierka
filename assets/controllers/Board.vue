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
            console.log('jestem drop', event, 'to mój target:', event.target);
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
            // TODO check canPlaceShipOnBoardField
            console.log('robie drag enter');
            this.changeColorFieldForDraggedShip();
        },
        onDragLeave(event) {
            // TODO check canPlaceShipOnBoardField
            console.log('robie drag leave');
            this.changeColorFieldForDraggedShip(false);
        },
        setAppropriateColorForAllFields() {
            for (let i = 0; i < gameState.boardArrangeFields.length; i++) {
                for (let j = 0; j < gameState.boardArrangeFields[i].length; j++) {
                    let field = gameState.boardArrangeFields[i][j];

                    if (field.shipPointer) {
                        field.htmlElement.style.backgroundColor = 'red';
                    } else if (field.isNextToShipPointers.length) {
                        field.htmlElement.style.backgroundColor = 'grey';
                    } else {
                        field.htmlElement.style.backgroundColor = 'hsl(140, 25%, 68%)';
                    }
                }
            }
        },
        canPlaceShipOnBoardField(htmlElement) {
            for (let i = 0; i < gameState.boardArrangeFields.length; i++) {
                for (let j = 0; j < gameState.boardArrangeFields[i].length; j++) {
                    let field = gameState.boardArrangeFields[i][j];
                    if (htmlElement == field.htmlElement) {
                        let movingFieldsToAction = this.getCoordinatesToAroundFields(i, j);

                        for (let k = 0; k < movingFieldsToAction.length; k++) {
                            if (!gameState.checkIfCoordinatesAreInBoardBoundary(movingFieldsToAction[k].row, movingFieldsToAction[k].column)) {
                                continue;
                            }

                            field = gameState.boardArrangeFields[movingFieldsToAction[k].row][movingFieldsToAction[k].column];
                            if (!field.isActive) {
                                return false;
                            }
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
        changeColorFieldForDraggedShip(addColor = true, color = 'blue') {
            let shipMovingProperty = this.targetsInBoardBoundary(event);
            if (shipMovingProperty) {
                shipMovingProperty.shipElements.forEach((shipElement) => {
                    let row = shipMovingProperty.targetCoordinates.row - (shipMovingProperty.shipSelectedElement.row - shipElement.row);
                    let column = shipMovingProperty.targetCoordinates.column - (shipMovingProperty.shipSelectedElement.column - shipElement.column);

                    // TODO move isActive condition to separate method
                    if (gameState.boardArrangeFields[row][column].isActive) {
                        gameState.boardArrangeFields[row][column].htmlElement.style.backgroundColor = addColor ? color : '';
                    }
                });
            }
        },
        targetsInBoardBoundary(event) {
            let shipElements = JSON.parse(event.dataTransfer.getData('shipElements'));
            let shipSelectedElement = shipElements[parseInt(event.dataTransfer.getData('shipSelectedElement')) - 1];

            let targetCoordinates = (() => {
                for (let i = 0; i < gameState.boardArrangeFields.length; i++) {
                    for (let j = 0; j < gameState.boardArrangeFields[i].length; j++) {
                        if (event.target === gameState.boardArrangeFields[i][j].htmlElement) {
                            return {row: i, column: j};
                        }
                    }
                }
            })();

            for (let i = 0; i < shipElements.length; i++) {
                let shipElement = shipElements[i];
                if (shipElement === shipSelectedElement) {
                    continue;
                }

                let checkedRow = targetCoordinates.row - (shipSelectedElement.row - shipElement.row);
                let checkedColumn = targetCoordinates.column - (shipSelectedElement.column - shipElement.column);
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