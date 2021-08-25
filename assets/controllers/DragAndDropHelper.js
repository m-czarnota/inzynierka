import {BoardField} from "./BoardField";
import {Ship} from "./Ship";

class DragAndDropHelper {
    constructor() {
        this.shipSelectedElement = -1;
        this.shipElements = [];
        this.selectedShipElement = null;
        this.servicedShip = null;
    }

    setDataToDrag(data) {
        this.servicedShip = data.ship;
        this.shipSelectedElement = data.shipSelectedElement;

        this.shipElements = JSON.parse(JSON.stringify(this.servicedShip.elementsGridProperties));
        this.selectedShipElement = this.shipElements[this.shipSelectedElement - 1];
    }

    onDragStart(event, data = null, shipElements = null, clear = false) {
        if (data) {
            this.setDataToDrag(data);
        }

        if (!this.servicedShip) {
            // TODO additionally set error above if it is necessary
            return;
        }

        event.dataTransfer.setData('ship', this.servicedShip.id);
        event.dataTransfer.setData('shipSelectedElement', this.shipSelectedElement);
        event.dataTransfer.setData('shipElements', JSON.stringify(shipElements ?? this.shipElements));

        this.setDragImage(event);

        if (clear) {
            this.servicedShip.boardFields.forEach(field => field.unblockField(this.servicedShip));
            this.servicedShip.aroundFields.forEach(field => field.unblockField(this.servicedShip));
            this.servicedShip.aroundFields = [];
        }
    }

    setDragImage(event) {
        /*
        Setting a drag image as dragged element.
        setDragImage method has inverted axes: ← +x | ↓ +y.
        setDragImage sets (0,0) on the most left and the most top squares.
        For the first element being the most left and the most top perfect start coordinates are (10,10).
        Algorithm:
        * find the most left grid value and the most top grid value (in grid they are min values)
        * substitute the most left grid value from column of selected element; accordingly with top and row
        * multiply these values by gridSize (place for one square in px)
        * add above values to 10
         */
        let left = Math.min.apply(Math, this.shipElements.map(shipElement => shipElement.column));
        let top = Math.min.apply(Math, this.shipElements.map(shipElement => shipElement.row));

        if (!BoardField.gridSize) {
            let cell = document.querySelector('.board-cell');
            BoardField.gridSize = cell.offsetWidth + parseInt(window.getComputedStyle(cell).marginLeft);
        }

        this.insertShipHtml();

        event.dataTransfer.setDragImage(
            this.servicedShip.fieldsParent,
            10 + (BoardField.gridSize * (this.selectedShipElement.column - left)),
            10 + (BoardField.gridSize * (this.selectedShipElement.row - top))
        );
    }

    insertShipHtml(selector = 'body') {
        if (!this.servicedShip) {
            return;
        }

        if (!document.querySelector(`#${Ship.clonedShipIdPrefix}${this.servicedShip.id}`)) {
            document.querySelector(selector).appendChild(this.servicedShip.fieldsParent);
        }
    }

    removeShipHtml(ship = this.servicedShip) {
        if (!ship) {
            return;
        }

        let clonedShipHtml = document.querySelector(`#${Ship.clonedShipIdPrefix}${ship.id}`);
        if (clonedShipHtml) {
            clonedShipHtml.remove();
        }
    }
}

export const dragAndDropHelper = new DragAndDropHelper();