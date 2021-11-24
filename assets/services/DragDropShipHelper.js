import {BoardField} from "../entities/game/BoardField";
import {Ship} from "../entities/game/Ship";
import {board} from "../entities/game/Board";
import {shipsStorage} from "../entities/game/ShipsStorage";
import {emitter} from "./Emitter";
import {shipPlacementService} from "./ShipPlacementService";

class DragDropShipHelper {
    constructor() {
        this.board = board;
        this.numberOfShipSelectedElement = -1;
        this.shipElements = [];
        this.selectedShipElement = null;
        this.servicedShip = null;
    }

    setDataToDrag(data) {
        this.servicedShip = data.ship;
        this.numberOfShipSelectedElement = data.numberOfShipSelectedElement;

        this.shipElements = JSON.parse(JSON.stringify(this.servicedShip.elementsGridProperties));
        this.selectedShipElement = this.shipElements[this.numberOfShipSelectedElement - 1];
    }

    onDragStart(event, dataTransfer = null, shipElements = null, additionalData = null) {
        if (dataTransfer) {
            this.setDataToDrag(dataTransfer);
        }

        if (!this.servicedShip) {
            // TODO additionally set error above if it is necessary
            return;
        }

        this.setDragImage(event);

        this.servicedShip.boardFields.forEach(field => field.unblockField(this.servicedShip));
        this.servicedShip.aroundFields.forEach(field => field.unblockField(this.servicedShip));
        this.servicedShip.aroundFields = [];

        this.setAppropriateColorForAllFields();
        this.setDataTransfer(event, shipElements, additionalData);
    }

    /**
     * Returns the basic transfer data.
     * Additional transfer data is not returned.
     * @param event
     * @returns {{shipElements: any, numberOfShipSelectedElement: number, shipId: string}}
     */
    getDataTransfer(event) {
        return {
            shipId: event.dataTransfer.getData('shipId'),
            numberOfShipSelectedElement: parseInt(event.dataTransfer.getData('numberOfShipSelectedElement')),
            shipElements: JSON.parse(event.dataTransfer.getData('shipElements'))
        };
    }

    /**
     * Sets basic transfer data for dragged ship.
     * Additional transfer data can be also setting.
     * @param event
     * @param shipElements
     * @param additionalData
     */
    setDataTransfer(event, shipElements = null, additionalData = null) {
        event.dataTransfer.setData('shipId', this.servicedShip.id);
        event.dataTransfer.setData('numberOfShipSelectedElement', this.numberOfShipSelectedElement);
        event.dataTransfer.setData('shipElements', JSON.stringify(shipElements ?? this.shipElements));

        shipPlacementService.defineCustomEvent(event.target, this.servicedShip, this.numberOfShipSelectedElement);

        if (additionalData) {
            Object.keys(additionalData).forEach(key => event.dataTransfer.setData(key, additionalData[key]));
            Object.keys(additionalData).forEach(key => shipPlacementService.customEvent.key = additionalData[key]);
        }
    }

    /**
     * Setting a drag image as dragged element.
     * setDragImage method has inverted axes: ← +x | ↓ +y.
     * setDragImage sets (0,0) on the most left and the most top squares.
     * For the first element being the most left and the most top perfect start coordinates are (10,10).
     * Algorithm:
     * - find the most left grid value and the most top grid value (in grid they are min values)
     * - substitute the most left grid value from column of selected element; accordingly with top and row
     * - multiply these values by gridSize (place for one square in px)
     * - add above values to 10
     * @param event
     */
    setDragImage(event) {
        const left = Math.min.apply(Math, this.shipElements.map(shipElement => shipElement.column));
        const top = Math.min.apply(Math, this.shipElements.map(shipElement => shipElement.row));

        if (!BoardField.gridSize) {
            const cell = document.querySelector('.board-cell');
            BoardField.gridSize = cell.offsetWidth + parseInt(window.getComputedStyle(cell).marginLeft);
        }

        this.insertShipHtml();

        event.dataTransfer.setDragImage(
            this.servicedShip.fieldsParent,
            10 + (BoardField.gridSize * (this.selectedShipElement.column - left)),
            10 + (BoardField.gridSize * (this.selectedShipElement.row - top))
        );
    }

    /**
     * Appends to document copy of ship HTML Element to proper working the dragImage.
     * @param selector
     */
    insertShipHtml(selector = '#ships-storage-helper') {
        if (!this.servicedShip) {
            return;
        }

        if (!document.querySelector('#ships-storage-helper')) {
            const div = document.createElement('div');
            div.id = 'ships-storage-helper';
            document.querySelector('body').appendChild(div);
        }

        this.removeShipHtml();
        document.querySelector(selector).appendChild(this.servicedShip.fieldsParent);
    }

    /**
     * Removes ship HTML Element added by this.insertShipHtml.
     * @param ship
     */
    removeShipHtml(ship = this.servicedShip) {
        if (!ship) {
            return;
        }

        const clonedShipHtml = document.querySelector(`#${Ship.clonedShipIdPrefix}${ship.id}`);
        if (clonedShipHtml) {
            clonedShipHtml.remove();
        }
    }

    onDrop(event) {
        if (!this.canPlaceShipOnBoardField(event)) {
            this.setAppropriateColorForAllFields();
            return;
        }

        const shipId = parseInt(event.dataTransfer.getData('shipId'));
        let ship = this.board.findShipById(shipId);

        // ship is coming from storage
        if (!ship) {
            ship = shipsStorage.ships.find(shipToFind => shipToFind.id === shipId);
            this.board.ships.push(ship);
            shipsStorage.ships.splice(shipsStorage.ships.indexOf(ship), 1);
        }

        // clear drag image element on drop
        dragDropShipHelper.removeShipHtml(ship);

        // allocate ships elements in suitable places
        const shipMovingProperty = this.prepareShipToPlaceOnBoard(event, ship);
        if (!shipMovingProperty) {
            return;
        }

        // drag in appropriate place, so reset boardFields for this ship
        ship.boardFields = [];
        clearTimeout(ship.timerToRestoreShipOnLastPosition);

        // place dragged ship on board
        this.placeDraggedShipOnBoard(shipMovingProperty, ship);

        // set draggable for placed ship
        this.activeDragForPlacedShip(event, ship);

        // update scope and other components
        emitter.emit('drop-complete', true);
    }

    /**
     * Places dragged ship on board.
     * Blocks ship's fields and fields around the ship.
     *
     * @param shipMovingProperty
     * @param ship
     */
    placeDraggedShipOnBoard(shipMovingProperty, ship) {
        shipMovingProperty.shipElements.forEach((shipElement, index) => {
            const shipField = this.board.getFieldForShipElement(shipMovingProperty, shipElement);

            // base operations
            shipField.shipPointer = ship.id;
            shipField.blockField(ship);
            shipField.numberOfShipElement = index + 1;
            shipField.htmlElement.classList.add('ship-element');
            ship.boardFields.push(shipField);
        });

        // block around fields
        shipMovingProperty.shipElements.forEach((shipElement, index) => {
            const coordinatesShip = this.board.calculateCoordinatesFieldForShipElement(shipMovingProperty, shipElement);
            const coordinatesToAroundFields = this.getCoordinatesToAroundFields(coordinatesShip.row, coordinatesShip.column);

            coordinatesToAroundFields.forEach(coordinates => {
                if (!this.board.checkIfCoordinatesAreInBoardBoundary(coordinates.row, coordinates.column)) {
                    return;
                }

                const fieldAroundShip = this.board.fields[coordinates.row][coordinates.column];
                fieldAroundShip.blockField(ship);
                if (!ship.boardFields.find(boardField => boardField.id === fieldAroundShip.id) && !ship.aroundFields.find(field => field.id === fieldAroundShip.id)) {
                    ship.aroundFields.push(fieldAroundShip);
                }
            });
        });

        this.setAppropriateColorForAllFields();
    }

    /**
     * Sets possibility to drag placed ship on board.
     * Also sets timer to restore ship, if drop will be executed in not suitable place.
     * @param event
     * @param ship
     */
    activeDragForPlacedShip(event, ship) {
        ship.boardFields.forEach(field => {
            field.htmlElement.setAttribute('draggable', 'true');
            field.htmlElement.ondrag = () => {
                clearTimeout(ship.timerToRestoreShipOnLastPosition);

                ship.timerToRestoreShipOnLastPosition = setTimeout(() => {
                    Object.defineProperty(event, 'target', {
                        value: ship.boardFields[this.getDataTransfer(event).numberOfShipSelectedElement - 1].htmlElement
                    });

                    if (this.board.findShipById(ship.id)) {
                        this.onDrop(event);
                    }
                }, ship.timeToRestoreShipOnLastPosition);
            };

            field.htmlElement.ondragstart = (event) => dragDropShipHelper.onDragStart(event, {
                ship: ship,
                numberOfShipSelectedElement: field.numberOfShipElement,
            }, ship.elementsGridProperties, {
                'dragFromBoard': true,
            });
        });
    }

    /**
     * Prepare the ship to place on the board before save.
     * @param event
     * @param ship
     * @returns {{shipSelectedElement: *, shipElements: *, targetCoordinates: {column, row: number}|undefined}|null}
     */
    prepareShipToPlaceOnBoard(event, ship) {
        let shipMovingProperty = this.targetsInBoardBoundary(event);
        if (!shipMovingProperty) {
            if (!ship.boardFields.length) {
                return null;
            }

            // now the field where is the cursor is a new target
            Object.defineProperty(event, 'target', {
                value: ship.boardFields[this.getDataTransfer(event).numberOfShipSelectedElement - 1].htmlElement
            });
            shipMovingProperty = this.targetsInBoardBoundary(event);
        }

        return shipMovingProperty;
    }

    onDragEnter(event) {
        // TODO change cursor on blocked when is cannot place the ship
        shipPlacementService.customEvent.target = event.target;
        this.changeColorFieldForDraggedShip(shipPlacementService.customEvent);
    }

    onDragLeave(event) {
        shipPlacementService.customEvent.target = event.target
        this.changeColorFieldForDraggedShip(shipPlacementService.customEvent, false);
    }

    /**
     * Sets appropriate color for fields depending on the their actual state and membership.
     */
    setAppropriateColorForAllFields(missAround = false) {
        for (let field of this.board.fields.flat()) {
            const htmlElement = field.htmlElement;
            htmlElement.classList.remove('ship-can-be-placed');

            if (field.shipPointer !== null) {
                htmlElement.classList.add('ship-element');
            } else if (field.isNextToShipPointers.length && missAround === false) {
                htmlElement.classList.add('next-to-ship');
            } else {
                htmlElement.classList.remove('ship-element', 'next-to-ship');
            }
        }
    }

    canPlaceShipOnBoardField(event) {
        for (let [index, field] of this.board.fields.flat().entries()) {
            if (event.target !== field.htmlElement) {
                continue;
            }

            const shipMovingProperty = this.targetsInBoardBoundary(event);
            if (!shipMovingProperty) {
                return false;
            }

            for (let shipElement of shipMovingProperty.shipElements) {
                const shipField = this.board.getFieldForShipElement(shipMovingProperty, shipElement);

                if (!shipField.isActive) {
                    return false;
                }
            }
        }

        return true;
    }

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
    }

    changeColorFieldForDraggedShip(event, addColor = true) {
        const shipMovingProperty = this.targetsInBoardBoundary(event);
        if (shipMovingProperty) {
            shipMovingProperty.shipElements.forEach((shipElement) => {
                const boardField = this.board.getFieldForShipElement(shipMovingProperty, shipElement);

                if (boardField.isActive) {
                    boardField.htmlElement.classList[addColor ? 'add' : 'remove']('ship-can-be-placed');
                }
            });
        }
    }

    targetsInBoardBoundary(event) {
        if (event.dataTransfer.getData('shipId') === null) {
            return null;
        }

        const dataTransfer = this.getDataTransfer(event);
        const shipSelectedElement = dataTransfer.shipElements[dataTransfer.numberOfShipSelectedElement - 1];

        const targetCoordinates = (() => {
            for (let [index, field] of this.board.fields.flat().entries()) {
                if (event.target === field.htmlElement) {
                    return this.board.calculateFieldPositionFromIndex(index);
                }
            }
        })();

        for (let shipElementGridValues of dataTransfer.shipElements) {
            if (!this.board.getFieldForShipElement(
                {targetCoordinates: targetCoordinates, shipSelectedElement: shipSelectedElement},
                shipElementGridValues
            )) {
                return null;
            }
        }

        return {
            targetCoordinates: targetCoordinates,
            shipElements: dataTransfer.shipElements,
            shipSelectedElement: shipSelectedElement
        };
    }

    storageOnDrop(event) {
        if (!event.dataTransfer.getData('dragFromBoard')) {
            return;
        }

        const dataTransfer = this.getDataTransfer(event);
        const ship = board.findShipById(dataTransfer.shipId);

        clearTimeout(ship.timerToRestoreShipOnLastPosition);
        // ship.actualPoseDecrement();

        shipsStorage.ships.push(ship);
        board.ships.splice(board.ships.indexOf(ship), 1);

        this.removeShipHtml(ship);
        ship.clean();
    }
}

export const dragDropShipHelper = new DragDropShipHelper();