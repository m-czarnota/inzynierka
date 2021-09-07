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
import {dragAndDropHelper} from "./DragAndDropHelper";
import {BoardField} from "./BoardField";
import {emitter} from "./Emitter";
import {shipsStorage} from "./ShipsStorage";
import {board} from "./Board";

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
            board.fields.push([]);
            row.querySelectorAll('.board-cell').forEach(fieldHtml => {
                let boardField = new BoardField();

                boardField.calculateCoordinates(fieldHtml.getAttribute('data-coordinates'));
                fieldHtml.removeAttribute('data-coordinates');
                boardField.htmlElement = fieldHtml;

                board.fields[index].push(boardField);
            });
        });
    },
    methods: {
        onDrop(event) {
            if (!this.canPlaceShipOnBoardField(event)) {
                this.setAppropriateColorForAllFields();
                return;
            }

            let shipId = parseInt(event.dataTransfer.getData('shipId'));
            let ship = board.findShipById(shipId);

            if (!ship) {
                ship = shipsStorage.ships.find(shipToFind => shipToFind.id === shipId);
                board.ships.push(ship);
                shipsStorage.ships.splice(shipsStorage.ships.indexOf(ship), 1);
            }

            dragAndDropHelper.removeShipHtml(ship);

            // allocate ships elements in suitable places
            let shipMovingProperty = this.targetsInBoardBoundary(event);
            if (!shipMovingProperty) {
                if (!ship.boardFields.length) {
                    return;
                }

                Object.defineProperty(event, 'target', {
                    value: ship.boardFields[parseInt(event.dataTransfer.getData('shipSelectedElement')) - 1].htmlElement
                });
                shipMovingProperty = this.targetsInBoardBoundary(event);
            }

            // drag in appropriate place, so reset boardFields for this ship
            ship.boardFields = [];
            clearTimeout(ship.timerToRestoreShipOnLastPosition);

            // place dragged ship on board
            shipMovingProperty.shipElements.forEach((shipElement, index) => {
                let coordinatesShip = board.calculateCoordinatesFieldForShipElement(shipMovingProperty, shipElement);
                let shipField = board.getFieldForShipElement(shipMovingProperty, shipElement);

                // base operations
                shipField.blockField();
                shipField.shipPointer = ship;
                shipField.numberOfShipElement = index + 1;
                shipField.htmlElement.style.backgroundColor = 'red';
                ship.boardFields.push(shipField);

                // block around fields
                let coordinatesToAroundFields = this.getCoordinatesToAroundFields(coordinatesShip.row, coordinatesShip.column);
                coordinatesToAroundFields.forEach(coordinates => {
                    if (!board.checkIfCoordinatesAreInBoardBoundary(coordinates.row, coordinates.column)) {
                        return;
                    }

                    let fieldAroundShip = board.fields[coordinates.row][coordinates.column];
                    if (!fieldAroundShip.shipPointer && !fieldAroundShip.isNextToShipPointers.find(item => item === ship)) {
                        // field does not belong to other ship
                        fieldAroundShip.blockField(ship);
                        ship.aroundFields.push(fieldAroundShip);
                    }
                });
            });

            // set draggable for placed ship
            ship.boardFields.forEach(field => {
                field.htmlElement.setAttribute('draggable', 'true');
                field.htmlElement.ondrag = () => {
                    clearTimeout(ship.timerToRestoreShipOnLastPosition);

                    ship.timerToRestoreShipOnLastPosition = setTimeout(() => {
                        Object.defineProperty(event, 'target', {
                            value: ship.boardFields[parseInt(event.dataTransfer.getData('shipSelectedElement')) - 1].htmlElement
                        });

                        if (board.findShipById(ship.id)) {
                            this.onDrop(event);
                        }
                    }, ship.timeToRestoreShipOnLastPosition);
                };

                field.htmlElement.ondragstart = (event) => dragAndDropHelper.onDragStart(event, {
                    ship: ship,
                    shipSelectedElement: field.numberOfShipElement,
                }, ship.elementsGridProperties, {
                    'dragFromBoard': true,
                });
            });

            // update scope and other components
            emitter.emit('drop-complete', true);
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
            for (let field of board.fields.flat()) {
                if (field.shipPointer) {
                    field.htmlElement.style.backgroundColor = 'red';
                } else if (field.isNextToShipPointers.length) {
                    field.htmlElement.style.backgroundColor = 'grey';
                } else {
                    field.htmlElement.style.backgroundColor = 'hsl(140, 25%, 68%)';
                }
            }
        },
        canPlaceShipOnBoardField(event) {
            for (let [index, field] of board.fields.flat().entries()) {
                if (event.target !== field.htmlElement) {
                    continue;
                }

                let shipMovingProperty = this.targetsInBoardBoundary(event);
                if (!shipMovingProperty) {
                    return false;
                }

                for (let shipElement of shipMovingProperty.shipElements) {
                    let shipField = board.getFieldForShipElement(shipMovingProperty, shipElement);

                    if (!shipField.isActive) {
                        return false;
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
                    let boardField = board.getFieldForShipElement(shipMovingProperty, shipElement);

                    if (boardField.isActive) {
                        boardField.htmlElement.style.backgroundColor = addColor ? color : '';
                    }
                });
            }
        },
        targetsInBoardBoundary(event) {
            if (!event.dataTransfer.getData('shipId')) {
                return null;
            }

            let shipElements = JSON.parse(event.dataTransfer.getData('shipElements'));
            let numberOfShipSelectedElement = parseInt(event.dataTransfer.getData('shipSelectedElement'));
            let shipSelectedElement = shipElements[numberOfShipSelectedElement - 1];

            let targetCoordinates = (() => {
                for (let [index, field] of board.fields.flat().entries()) {
                    if (event.target === field.htmlElement) {
                        return board.calculateFieldPositionFromIndex(index);
                    }
                }
            })();

            for (let shipElementGridValues of shipElements) {
                if (!board.getFieldForShipElement(
                    {targetCoordinates: targetCoordinates, shipSelectedElement: shipSelectedElement},
                    shipElementGridValues
                )) {
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