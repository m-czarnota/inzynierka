import {board} from "./Board";

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
        this.fieldsParent.style.top = '-1000px';
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

    static createInstanceFromParsedObject(parsedShip) {
        const getParsedBoardField = parsedBoardField => {
            const boardField = board.getFieldByCoordinates(parsedBoardField.coordinates);
            boardField.numberOfShipElement = parsedBoardField.numberOfShipElement;
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
        ship.htmlElements = parsedShip.htmlElements;  // to fix
        ship.fieldsParent = parsedShip.fieldsParent;
        ship.poses = parsedShip.poses;
        ship.actualPose = parsedShip.actualPose;
        ship.wasFirstRotate = parsedShip.wasFirstRotate;

        ship.timeToRestoreShipOnLastPosition = parsedShip.timeToRestoreShipOnLastPosition;

        return ship;
    }
}
