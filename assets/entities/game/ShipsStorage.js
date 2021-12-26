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
            for (let elements in [...Array(this.getNumberOfSpecifyShips(item))]) {
                const ship = new Ship();
                ship.elementsCount = this.getNumberOfSpecifyShipElement(item);
                ship.poses = ShipPoses[ship.elementsCount];
                this.ships.push(ship);
            }
        });
    }

    remove() {
        this.ships.forEach(ship => ship.remove());
        delete this;
    }

    getNumberOfSpecifyShips(numberOfParticularShip) {
        return Object.values(numberOfParticularShip)[0];
    }

    getNumberOfSpecifyShipElement(numberOfParticularShip) {
        return parseInt(Object.keys(numberOfParticularShip)[0]);
    }
}

export const shipsStorage = new ShipsStorage();