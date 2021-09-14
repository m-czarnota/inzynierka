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
        this.fieldsParent = null;
        this.poses = [];
        this.actualPose = 0;

        this.timerToRestoreShipOnLastPosition = null;
        this.timeToRestoreShipOnLastPosition = 1000;
    }

    rotate(event) {
        const target = event instanceof HTMLElement ? event : event.target.previousSibling;
        this.elementsGridProperties = [];

        $(target).children().each((index, element) => {
            $(element).css(this.poses[this.actualPose][index]);
            this.elementsGridProperties.push({
                column: $(element).css('gridColumnStart'),
                row: $(element).css('gridRowStart'),
            });
        });

        this.actualPoseIncrement();
        this.cloneNode(target);
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
}
