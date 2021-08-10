import {ShipPoses} from "./ShipPoses";
import {Ship} from "./Ship";

class GameState {
    constructor() {
        this.numberOfParticularShips = [
            {4: 1},
            {3: 2},
            {2: 3},
            {1: 4},
        ];
        this.shipsToDragging = [];
        this.boardArrangeFields = [];

        this.prepareShips();
    }

    prepareShips() {
        this.numberOfParticularShips.forEach(item => {
            for (let i = 0; i < item[Object.keys(item)[0]]; i++) {
                let ship = new Ship();
                ship.elementsCount = i + 1;
                ship.poses = ShipPoses[ship.elementsCount];
                this.shipsToDragging.push(ship);
            }
        });
    }

    checkIfCoordinatesAreInBoardBoundary(row, column) {
        return !(column < 0 || column >= this.boardArrangeFields.length || row < 0 || row >= this.boardArrangeFields.length);
    }

    checkIfCoordinatesAreInBoardBoundaryFromIndex(index = -1) {
        if (index === -1) {
            return false;
        }

        let fieldPosition = this.calculateFieldPositionFromIndex(index);
        return !(fieldPosition.column < 0 || fieldPosition.column >= this.boardArrangeFields.length || fieldPosition.row < 0 || fieldPosition.row >= this.boardArrangeFields.length);
    }

    calculateFieldPositionFromIndex(index = -1) {
        return {
            row: Math.trunc(index / this.boardArrangeFields.length),
            column: index % this.boardArrangeFields.length
        }
    }
}

export const gameState = new GameState();