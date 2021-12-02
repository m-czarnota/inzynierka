<template>
    <div class="ship-component">
        <div class="board-ship"
             ref="x"
             :aria-label="ship.elementsCount"
        >
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
        <div class="buttons mt-2 pt-2 border-top w-100 d-flex align-items-center justify-content-center"
             v-if="ship.elementsCount > 1"
        >
            <button @click="ship.rotate()"
                    v-if="ship.elementsCount > 1"
                    class="btn btn-light btn-outline-dark">Rotate</button>
        </div>
    </div>
</template>

<script>
import FieldComponent from "./FieldComponent";
import {BoardField} from "../../entities/game/BoardField";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";

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

        if (this.ship.wasFirstRotate) {
            this.ship.actualPoseDecrement();
        }

        this.ship.htmlElements = shipNode;
        this.ship.rotate();
    },
}
</script>

<style scoped>

</style>