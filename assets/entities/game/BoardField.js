let id = 0;

export class BoardField {
    static gridSize = null;
    static startCoordinatesLetter = 'A';

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
        let char = parseInt(this.coordinates.substring(0, center)) + BoardField.startCoordinatesLetter.charCodeAt(0) - 1;
        let number = this.coordinates.substring(center);

        if (number.length === 2 && number[0] === '0') {
            char += 9;
            number = number.substring(1);
        }

        this.coordinates = String.fromCharCode(char) + number;
    }

    blockField(shipPointer) {
        this.isActive = false;
        if (this.shipPointer === null && !this.isNextToShipPointers.includes(shipPointer.id)) {
            this.isNextToShipPointers.push(shipPointer.id);
        }

        // TODO better styles assign
        this.htmlElement.style.backgroundColor = 'grey';
        this.htmlElement.style.cursor = 'default';
    }

    unblockField(shipPointer) {
        this.shipPointer = null;
        this.isNextToShipPointers.splice(this.isNextToShipPointers.indexOf(shipPointer.id), 1);

        this.numberOfShipElement = -1;
        this.htmlElement.removeAttribute('draggable');

        if (!this.isNextToShipPointers.length) {
            this.isActive = true;
            this.htmlElement.style.backgroundColor = '';
            this.htmlElement.style.cursor = '';
        }
    }

    setHitStatus() {
        if (this.shipPointer === -1) {
            return;
        }

        this.isHit = true;
        this.htmlElement.backgroundColor = 'brown';
    }

    setKilledStatus() {
        if (this.numberOfShipElement === -1) {
            return;
        }

        this.isHit = true;
        this.htmlElement.backgroundColor = 'purple';
    }

    setInactiveStatus() {
        if (this.isNextToShipPointers.length === 0 || this.isHit === false) {
            return;
        }

        this.htmlElement.backgroundColor = 'gray';
    }

    remove() {
        delete this;
    }
}