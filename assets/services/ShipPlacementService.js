import {board} from "../entities/game/Board";
import {shipsStorage} from "../entities/game/ShipsStorage";
import {dragDropShipHelper} from "./DragDropShipHelper";

class ShipPlacementService {
    constructor() {
        this.customEvent = null
    }

    defineCustomEvent(data) {
        this.customEvent = {
            target: data.target,
            dataTransfer: {
                shipId: data.ship.id,
                numberOfShipSelectedElement: data.selectedElement,
                shipElements: JSON.stringify(data.ship.elementsGridProperties),
                getData(key) {
                    return this[key];
                },
            },
        };
    }

    drawShipToPlaceOnBoard(data) {
        const targetElement = this.drawFieldOnBoard();

        this.defineCustomEvent({
            target: targetElement,
            ship: data.ship,
            selectedElement: data.selectedElement,
        });
    }

    drawActualPose(ship) {
        ship.actualPose = Math.random() * ship.poses.length | 0;
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

            this.drawShipToPlaceOnBoard({
                ship: ship,
                selectedElement: selectedElement,
            });

            if (loopCount >= stopCondition) {
                this.drawActualPose(ship);
                selectedElement = this.drawSelectedElement(ship);
                loopCount = 0;
            }
        } while (!dragDropShipHelper.canPlaceShipOnBoardField(this.customEvent));

        dragDropShipHelper.onDrop(this.customEvent);
    }
}

export const shipPlacementService = new ShipPlacementService();
