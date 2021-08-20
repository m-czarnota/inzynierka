<template>
    <div class="board-ship" ref="x">
        <field-component :coordinates-prop="'A1'" v-for="n in ship.elementsCount" class="board-ship-element"
                         :data-element-number="n" draggable="true"
                         @dragstart="onDragStart($event)"></field-component>
    </div>
    <button @click="ship.rotate($event)" v-if="ship.elementsCount > 1">Rotate</button>
</template>

<script>
import FieldComponent from "./FieldComponent";

const $ = require('jquery');

let id = 0;

export default {
    name: "ShipComponent",
    components: {FieldComponent},
    data() {
        return {
            id: id++,
            isFirstRotate: false,
            gridSize: 0,
        }
    },
    props: ['elementsCountProp', 'ship'],
    methods: {
        onDragStart(event) {
            let shipSelectedElement = parseInt(event.target.getAttribute('data-element-number'));
            let shipElements = JSON.parse(JSON.stringify(this.ship.elementsGridProperties))
            let selectedShipElement = shipElements[shipSelectedElement - 1];

            event.dataTransfer.setData('ship', this.ship.id);
            event.dataTransfer.setData('shipSelectedElement', event.target.getAttribute('data-element-number'));
            event.dataTransfer.setData('shipElements', JSON.stringify(shipElements));

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
            let left = Math.min.apply(Math, shipElements.map(shipElement => shipElement.column));
            let top = Math.min.apply(Math, shipElements.map(shipElement => shipElement.row));
            event.dataTransfer.setDragImage(
                event.target.parentNode,
                10 + (this.gridSize * (selectedShipElement.column - left)),
                10 + (this.gridSize * (selectedShipElement.row - top))
            );

            // TODO apply above algorithm to any ondragstart
            // TODO create DragApiHelper
        }
    },
    mounted() {
        let ship = this.$refs.x;
        let shipElementSize = ship.querySelector('.board-cell').offsetWidth;
        this.gridSize = shipElementSize + 10;

        ship.style.display = 'grid';
        ship.style.gridTemplateColumns = '';
        ship.style.gridTemplateRows = '';

        for (let i = 0; i < this.ship.elementsCount; i++) {
            ship.style.gridTemplateColumns += `[column${i + 1}] ${this.gridSize}px `;
            ship.style.gridTemplateRows += `[row${i + 1}] ${this.gridSize}px `;
        }

        if (!this.isFirstRotate) {
            this.ship.rotate(ship.nextSibling);
            this.isFirstRotate = true;
        }
    },
}
</script>

<style scoped>

</style>