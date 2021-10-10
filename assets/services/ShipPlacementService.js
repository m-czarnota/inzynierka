import {board} from "../entities/game/Board";
import {shipsStorage} from "../entities/game/ShipsStorage";
import {dragDropShipHelper} from "./DragDropShipHelper";
import {Ship} from "../entities/game/Ship";
import {gameState} from "./GameState";

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

        gameState.saveInfoToStorage();
    }

    stringifyShips(ships) {
        return JSON.stringify(ships);
    }

    parseShips(shipsJson) {
        const parsedShips = JSON.parse(shipsJson);
        const ships = [];

        parsedShips.forEach(parsedShip => ships.push(Ship.createInstanceFromParsedObject(parsedShip)));

        return ships;
    }
}

export const shipPlacementService = new ShipPlacementService();
