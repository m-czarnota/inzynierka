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
        this.htmlElement.style.cursor = 'default';
        if (this.shipPointer) {
            this.htmlElement.classList.add('ship-element');
        } else if (this.isNextToShipPointers.includes(shipPointer.id)) {
            this.htmlElement.classList.add('next-to-ship');
        }
    }

    unblockField(shipPointer) {
        this.shipPointer = null;
        this.isNextToShipPointers.splice(this.isNextToShipPointers.indexOf(shipPointer.id), 1);

        this.numberOfShipElement = -1;
        this.htmlElement.removeAttribute('draggable');
        this.htmlElement.classList.remove('ship-element');

        if (!this.isNextToShipPointers.length) {
            this.isActive = true;
            this.htmlElement.classList.remove('next-to-ship');
            this.htmlElement.style.cursor = '';
        }
    }

    setShotStatus(withHit = true) {
        this.isHit = withHit;
        this.htmlElement.classList.remove('active');
        this.htmlElement.classList.add('inactive');
        this.htmlElement.onclick = null;
    }

    setMisHitStatus() {
        this.setShotStatus();
        this.htmlElement.classList.add('miss');
    }

    setHitStatus() {
        this.setShotStatus();
        this.htmlElement.classList.add('hit');
    }

    setKilledStatus() {
        this.setShotStatus();
        this.htmlElement.classList.add('killed');
    }

    setInactiveStatus() {
        this.setShotStatus(false);
        this.htmlElement.classList.add('next-to-ship');
    }

    remove() {
        delete this;
    }
}