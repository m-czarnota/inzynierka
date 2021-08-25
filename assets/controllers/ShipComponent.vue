<template>
    <div class="board-ship" ref="x">
        <field-component :coordinates-prop="'A1'" v-for="n in ship.elementsCount" class="board-ship-element"
                         :data-element-number="n" draggable="true"
                         @dragstart="dragAndDropHelper.onDragStart($event, {
                            ship: ship,
                            shipSelectedElement: n
                        })"></field-component>
    </div>
    <button @click="ship.rotate($event)" v-if="ship.elementsCount > 1">Rotate</button>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {BoardField} from "./BoardField";
import {dragAndDropHelper} from "./DragAndDropHelper";
import {Ship} from "./Ship";

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
            dragAndDropHelper: dragAndDropHelper
        }
    },
    props: ['elementsCountProp', 'ship'],
    methods: {},
    mounted() {
        let ship = this.$refs.x;
        let shipElementSize = ship.querySelector('.board-cell').offsetWidth;
        this.gridSize = shipElementSize + 10;
        BoardField.gridSize = this.gridSize;

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

        this.ship.fieldsParent = ship.cloneNode(true);
        this.ship.fieldsParent.id = Ship.clonedShipIdPrefix + this.ship.id;
        this.ship.fieldsParent.style.position = 'absolute';
        this.ship.fieldsParent.style.top = '-1000px';
    },
}
</script>

<style scoped>

</style>