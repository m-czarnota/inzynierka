import {Ship} from "./Ship";

class Board {
    constructor() {
        this.size = 10;
        this.wasFirstMount = false;
        this.ships = [];
        this.fields = [];
    }

    findShipById(id) {
        id = parseInt(id);
        return this.ships.find(ship => ship && ship.id === id);
    }

    checkIfCoordinatesAreInBoardBoundary(row, column) {
        return !(column < 0 || column >= this.fields.length || row < 0 || row >= this.fields.length);
    }

    checkIfCoordinatesAreInBoardBoundaryFromIndex(index = -1) {
        if (index === -1) {
            return false;
        }

        let fieldPosition = this.calculateFieldPositionFromIndex(index);
        return !(fieldPosition.column < 0 || fieldPosition.column >= this.fields.length || fieldPosition.row < 0 || fieldPosition.row >= this.fields.length);
    }

    calculateFieldPositionFromIndex(index = -1) {
        return {
            row: Math.trunc(index / this.fields.length),
            column: index % this.fields.length
        }
    }

    calculateCoordinatesFieldForShipElement(shipMovingProperty, shipElement) {
        return {
            row: shipMovingProperty.targetCoordinates.row - (shipMovingProperty.shipSelectedElement.row - shipElement.row),
            column: shipMovingProperty.targetCoordinates.column - (shipMovingProperty.shipSelectedElement.column - shipElement.column)
        }
    }

    getFieldForShipElement(shipMovingProperty, shipElement) {
        let coordinates = this.calculateCoordinatesFieldForShipElement(shipMovingProperty, shipElement);

        return this.checkIfCoordinatesAreInBoardBoundary(coordinates.row, coordinates.column) ?
            this.fields[coordinates.row][coordinates.column] : null;
    }
}

export const board = new Board();