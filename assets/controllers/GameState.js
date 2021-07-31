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
}

export const gameState = new GameState();