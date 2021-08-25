const $ = require("jquery");

let id = 0;

export class Ship {
    static clonedShipIdPrefix = 'clonedShip';

    constructor() {
        this.id = id++;
        this.elementsCount = 0;
        this.elementsGridProperties = [];
        this.boardFields = [];
        this.aroundFields = [];
        this.hitElements = [];
        this.fieldsParent = null;
        this.poses = [];
        this.actualPose = 0;

        this.timerToRestoreShipOnLastPosition = null;
        this.timeToRestoreShipOnLastPosition = 1000;
    }

    rotate(event) {
        let target = event instanceof HTMLElement ? event : event.target;
        this.elementsGridProperties = [];

        $(target).prev().children().each((index, element) => {
            $(element).css(this.poses[this.actualPose][index]);
            this.elementsGridProperties.push({
                column: $(element).css('gridColumnStart'),
                row: $(element).css('gridRowStart'),
            });
        });
        this.actualPose++;

        if (this.actualPose >= this.poses.length) {
            this.actualPose = 0;
        }
    }
}
