import {Ship} from "./Ship";
import {ShipPoses} from "./ShipPoses";

class ShipsStorage {
    constructor() {
        this.numberOfParticularShips = [
            {4: 1},
            {3: 2},
            {2: 3},
            {1: 4},
        ];
        this.ships = [];

        this.prepareShips();
    }

    prepareShips() {
        this.numberOfParticularShips.forEach(item => {
            for (let i = 0; i < item[Object.keys(item)[0]]; i++) {
                const ship = new Ship();
                ship.elementsCount = i + 1;
                ship.poses = ShipPoses[ship.elementsCount];
                this.ships.push(ship);
            }
        });
    }
}

export const shipsStorage = new ShipsStorage();