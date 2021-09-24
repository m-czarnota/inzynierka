let id = 0;

export class BoardField {
    static gridSize = null;

    constructor() {
        this.id = id++;
        this.shipPointer = null;  // in which ship does this element belong
        this.numberOfShipElement = -1;  // number of ship element
        this.isNextToShipPointers = [];  // this field is next to the following ships
        this.isHit = false;
        this.coordinates = null;  // coordinates on board
        this.htmlElement = null;  // suitable div
        this.isActive = true;
    }

    calculateCoordinates(coordinates = null) {
        this.coordinates = coordinates ?? this.coordinates;

        const center = Math.floor(this.coordinates.length / 2);
        let char = parseInt(this.coordinates.substring(0, center)) + 'A'.charCodeAt(0);
        let number = this.coordinates.substring(center);

        if (number.length === 2 && number[0] === '0') {
            char += 9;
            number = number.substring(1);
        }

        this.coordinates = String.fromCharCode(char) + number;
    }

    blockField(shipPointer) {
        this.isActive = false;
        this.isNextToShipPointers.push(shipPointer);

        // TODO better styles assign
        this.htmlElement.style.backgroundColor = 'grey';
        this.htmlElement.style.cursor = 'default';
    }

    unblockField(shipPointer) {
        this.shipPointer = null;
        this.isNextToShipPointers.splice(this.isNextToShipPointers.indexOf(shipPointer), 1);

        this.numberOfShipElement = -1;
        this.htmlElement.removeAttribute('draggable');

        if (!this.isNextToShipPointers.length) {
            this.isActive = true;
            this.htmlElement.style.backgroundColor = '';
            this.htmlElement.style.cursor = '';
        }
    }
}