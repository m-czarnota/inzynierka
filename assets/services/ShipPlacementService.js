import {board} from "../entities/game/Board";

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
                shipElements: data.shipElements,
                getData(key) {
                    return this[key];
                },
            },
        };
    }

    drawShipToPlaceOnBoard(ship) {
        ship.actualPose = Math.random() * ship.poses.length | 0;
        const selectedElement = Math.random() * ship.elementsCount + 1 | 0;
        const targetElement = this.drawFieldOnBoard();

        this.defineCustomEvent({
            target: targetElement,
            ship: ship,
            selectedElement: selectedElement,
            shipElements: JSON.stringify(ship.elementsGridProperties),
        });
    }

    drawFieldOnBoard() {
        const boardFieldsFlat = board.fields.flat();
        const drawIndexBoardFieldsFlat = Math.random() * boardFieldsFlat.length | 0;
        return boardFieldsFlat[drawIndexBoardFieldsFlat].htmlElement;
    }
}

export const shipPlacementService = new ShipPlacementService();
