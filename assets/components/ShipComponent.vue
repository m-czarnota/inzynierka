<template>
    <div class="ship-component">
        <div class="board-ship" ref="x" :aria-label="ship.elementsCount">
            <field-component :coordinates-prop="'A1'"
                             v-for="n in ship.elementsCount"
                             class="board-ship-element"
                             :data-element-number="n"
                             draggable="true"
                             @dragstart="dragDropShipHelper.onDragStart($event, {
                                ship: ship,
                                numberOfShipSelectedElement: n
                            })"></field-component>
        </div>
        <button @click="ship.rotate($event)" v-if="ship.elementsCount > 1">Rotate</button>
    </div>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {BoardField} from "../entities/BoardField";
import {dragDropShipHelper} from "../services/DragDropShipHelper";

const $ = require('jquery');

let id = 0;

export default {
    name: "ShipComponent",
    components: {FieldComponent},
    data() {
        return {
            id: id++,
            dragDropShipHelper: dragDropShipHelper,
        }
    },
    props: ['elementsCountProp', 'ship'],
    methods: {},
    mounted() {
        const shipNode = this.$refs.x;
        const shipElementSize = shipNode.querySelector('.board-cell').offsetWidth;

        const gridSize = shipElementSize + 10;
        BoardField.gridSize = gridSize;

        shipNode.style.display = 'grid';
        shipNode.style.gridTemplateColumns = '';
        shipNode.style.gridTemplateRows = '';

        for (let i = 0; i < this.ship.elementsCount; i++) {
            shipNode.style.gridTemplateColumns += `[column${i + 1}] ${gridSize}px `;
            shipNode.style.gridTemplateRows += `[row${i + 1}] ${gridSize}px `;
        }

        this.ship.rotate(shipNode);
    },
}
</script>

<style scoped>

</style>