const $ = require("jquery");

let id = 0;

export class Ship {
    constructor() {
        this.id = id++;
        this.elementsCount = 0;
        this.elementsGridProperties = [];
        this.boardFields = [];
        this.aroundFields = [];
        this.hitElements = [];
        this.poses = [];
        this.actualPose = 0;
    }

    rotate(event) {
        let target = event instanceof HTMLElement ? event : event.target;
        $(target).prev().children().each((index, element) => {
            $(element).css(this.poses[this.actualPose][index]);
        });
        this.actualPose++;

        if (this.actualPose >= this.poses.length) {
            this.actualPose = 0;
        }
    }
}
