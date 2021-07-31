<template>
    <div class="board-ship" ref="x">
        <field :coordinates-prop="'A1'" v-for="n in ship.elementsCount" class="board-ship-element"
               :data-element-number="n" draggable="true"
               @dragstart="onDragStart($event)"></field>
    </div>
    <button @click="ship.rotate($event)" v-if="ship.elementsCount > 1">Rotate</button>
</template>

<script>
import Field from "./Field";

const $ = require('jquery');

let id = 0;

export default {
    name: "ShipComponent",
    components: {Field},
    data() {
        return {
            id: id++,
        }
    },
    props: ['elementsCountProp', 'ship'],
    methods: {
        onDragStart(event) {
            event.dataTransfer.setData('ship', this.ship.id);
            event.dataTransfer.setData('shipSelectedElement', event.target.getAttribute('data-element-number'));

            let elements = [];
            event.target.parentNode.querySelectorAll('.board-ship-element').forEach(div => {
                elements.push({
                    column: div.style.gridColumnStart,
                    row: div.style.gridRowStart,
                });
            });

            console.log(JSON.stringify(elements));
            event.dataTransfer.setData('shipElements', JSON.stringify(elements));
        }
    },
    mounted() {
        let ship = this.$refs.x;
        let shipElementSize = ship.querySelector('.board-cell').offsetWidth;
        let gridSize = shipElementSize + 10;

        ship.style.display = 'grid';
        ship.style.gridTemplateColumns = '';
        ship.style.gridTemplateRows = '';

        for (let i = 0; i < this.ship.elementsCount; i++) {
            ship.style.gridTemplateColumns += `[column${i + 1}] ${gridSize}px `;
            ship.style.gridTemplateRows += `[row${i + 1}] ${gridSize}px `;
        }

        this.ship.rotate(ship.nextSibling);
    },
}
</script>

<style scoped>

</style>