<template>
    <div class="board-ship" draggable="true" @dragstart="onDragStart($event)">
        <div v-for="n in ship.elementsCount" class="board-cell board-ship-element" ref="x"></div>
    </div>
    <button @click="ship.rotate($event)" v-if="ship.elementsCount > 1">Rotate</button>
</template>

<script>
const $ = require('jquery');

let id = 0;

export default {
    name: "ShipComponent",
    data() {
        return {
            id: id++,
        }
    },
    props: ['elementsCountProp', 'ship'],
    methods: {
        onDragStart(event) {
            event.dataTransfer.setData('ship', this.ship.id);
        }
    },
    mounted() {
        let ship = this.$refs.x.parentNode;
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