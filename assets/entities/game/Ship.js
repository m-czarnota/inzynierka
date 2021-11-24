const $ = require("jquery");

let id = 0;

export class Ship {
    static clonedShipIdPrefix = 'clonedShipNode';

    constructor() {
        this.id = id++;
        this.elementsCount = 0;
        this.elementsGridProperties = [];
        this.boardFields = [];
        this.aroundFields = [];
        this.hitElements = [];
        this.htmlElements = null;
        this.fieldsParent = null;
        this.poses = [];
        this.actualPose = 0;
        this.wasFirstRotate = false;

        this.timerToRestoreShipOnLastPosition = null;
        this.timeToRestoreShipOnLastPosition = 1000;
    }

    rotate() {
        this.elementsGridProperties = [];
        this.wasFirstRotate = true;

        $(this.htmlElements).children().each((index, element) => {
            $(element).css(this.poses[this.actualPose][index]);
            this.elementsGridProperties.push({
                column: $(element).css('gridColumnStart'),
                row: $(element).css('gridRowStart'),
            });
        });

        this.actualPoseIncrement();
        this.cloneNode(this.htmlElements);
    }

    cloneNode(node) {
        this.fieldsParent = node.cloneNode(true);
        this.fieldsParent.id = Ship.clonedShipIdPrefix + this.id;
        this.fieldsParent.style.position = 'absolute';
        this.fieldsParent.style.top = (-this.id + 1) * 1000 + 'px';
    }

    actualPoseIncrement() {
        this.actualPose++;

        if (this.actualPose >= this.poses.length) {
            this.actualPose = 0;
        }
    }

    actualPoseDecrement() {
        this.actualPose--;

        if (this.actualPose < 0) {
            this.actualPose = 0;
        }
    }

    setKilledStatus() {
        this.boardFields.forEach(field => {
            field.setKilledStatus();

            if (!this.hitElements.includes(field)) {
                this.hitElements.push(field);
            }
        });
        this.aroundFields.forEach(field => field.setInactiveStatus());
    }

    isKilled() {
        return this.hitElements.length === this.elementsCount;
    }

    static createInstanceFromParsedObject(parsedShip, board) {
        const getParsedBoardField = parsedBoardField => {
            const boardField = board.getFieldByCoordinates(parsedBoardField.coordinates);
            boardField.shipPointer = parsedBoardField.shipPointer;
            boardField.numberOfShipElement = parsedBoardField.numberOfShipElement;
            boardField.isNextToShipPointers = parsedBoardField.isNextToShipPointers;
            boardField.isHit = parsedBoardField.isHit;
            boardField.isActive = parsedBoardField.isActive;

            return boardField;
        };

        const ship = new Ship();

        ship.id = parsedShip.id;
        ship.elementsCount = parsedShip.elementsCount;
        ship.elementsGridProperties = parsedShip.elementsGridProperties;
        parsedShip.boardFields.forEach(boardField => ship.boardFields.push(getParsedBoardField(boardField)));
        parsedShip.aroundFields.forEach(aroundField => ship.aroundFields.push(getParsedBoardField(aroundField)));
        ship.hitElements = parsedShip.hitElements;
        ship.fieldsParent = parsedShip.fieldsParent;
        ship.poses = parsedShip.poses;
        ship.actualPose = parsedShip.actualPose;
        ship.wasFirstRotate = parsedShip.wasFirstRotate;

        ship.timeToRestoreShipOnLastPosition = parsedShip.timeToRestoreShipOnLastPosition;

        return ship;
    }

    remove() {
        this.aroundFields.forEach(field => field.remove());
        this.boardFields.forEach(field => field.remove());
        delete this;
    }

    clean() {
        this.boardFields.forEach(boardField => {
            boardField.numberOfShipElement = -1;
            boardField.shipPointer = null;
            boardField.htmlElement.removeAttribute('draggable');
            boardField.isActive = true;
        });
        this.boardFields = [];

        this.aroundFields.forEach(aroundField => {
            aroundField.isNextToShipPointers.splice(aroundField.isNextToShipPointers.indexOf(this.id), 1);
            aroundField.isActive = true;
        });
        this.aroundFields = [];
    }
}
