import {board} from "../entities/game/Board";
import {shipsStorage} from "../entities/game/ShipsStorage";
import {dragDropShipHelper} from "./DragDropShipHelper";

class ShipPlacementService {
    constructor() {
        this.customEvent = null
    }

    defineCustomEvent(target, ship, selectedElement) {
        this.customEvent = {
            target: target,
            dataTransfer: {
                shipId: ship.id,
                numberOfShipSelectedElement: selectedElement,
                shipElements: JSON.stringify(ship.elementsGridProperties),
                getData(key) {
                    return this[key];
                },
            },
        };
    }

    drawShipToPlaceOnBoard(ship, selectedElement) {
        const targetElement = this.drawFieldOnBoard();

        this.defineCustomEvent(targetElement, ship, selectedElement);
    }

    drawActualPose(ship) {
        ship.actualPose = Math.random() * ship.poses.length | 0;
        ship.rotate();
        return ship.actualPose;
    }

    drawSelectedElement(ship) {
        return Math.random() * ship.elementsCount + 1 | 0;
    }

    drawFieldOnBoard() {
        const boardFieldsFlat = board.fields.flat();
        const drawIndexBoardFieldsFlat = Math.random() * boardFieldsFlat.length | 0;
        return boardFieldsFlat[drawIndexBoardFieldsFlat].htmlElement;
    }

    * reverseKeys(array) {
        let key = array.length - 1;

        while (key >= 0) {
            yield key;
            key -= 1;
        }
    }

    autoPlaceAllShips() {
        for (let index of this.reverseKeys(shipsStorage.ships)) {
            const ship = shipsStorage.ships[index];
            this.autoPlaceShip(ship);
        }
    }

    autoPlaceShip(ship) {
        this.drawActualPose(ship);
        let selectedElement = this.drawSelectedElement(ship);

        const stopCondition = 5000;
        let loopCount = 0;

        do {
            loopCount++;

            this.drawShipToPlaceOnBoard(ship, selectedElement);

            if (loopCount >= stopCondition) {
                this.drawActualPose(ship);
                selectedElement = this.drawSelectedElement(ship);
                loopCount = 0;
            }
        } while (!dragDropShipHelper.canPlaceShipOnBoardField(this.customEvent));

        dragDropShipHelper.onDrop(this.customEvent);
    }

    findShipInArray(ship, array) {
        return array.find(item => item.id === ship.id);
    }

    stringifyShips(ships) {
        ships.forEach(ship => {
            ship.boardFields.forEach(boardField => boardField.isNextToShipPointers = [...new Set(boardField.isNextToShipPointers)]);
            ship.aroundFields.forEach(aroundField => aroundField.isNextToShipPointers = [...new Set(aroundField.isNextToShipPointers)]);
        });

        return JSON.stringify(ships, (key, val) => {
            if (val instanceof HTMLElement) {
                return null;
            }

            return val;
        });
    }
}

export const shipPlacementService = new ShipPlacementService();
