<template>
    <div class="board-cell" ref="board-cell"
         @click.once="shot"
         @drop="onDrop($event)"
         @dragenter="onDragEnter($event)"
         @dragleave="onDragLeave($event)"
         @dragover.prevent></div>
</template>

<script>
import {gameState} from "./GameState";

let id = 0

export default {
    name: "Field",
    data() {
        return {
            id: id++,
            shipElement: null,
            isHit: false,
            coordinates: this.coordinatesProp,
        };
    },
    props: ['coordinatesProp'],
    methods: {
        onDrop(event) {
            console.log('jestem drop', event, 'to mój target:', event.target);
            let shipId = parseInt(event.dataTransfer.getData('ship'));
            let ship = gameState.shipsToDragging.find(shipToFind => shipToFind.id === shipId);
            // gameState.shipsToDragging.splice(gameState.shipsToDragging.indexOf(ship), 1);

            // allocate ships elements in suitable places
        },
        onDragEnter(event) {
            event.target.style.backgroundColor = '#efefef';
            console.log('robie drag enter');
        },
        onDragLeave(event) {
            event.target.style.backgroundColor = "";
            console.log('robie drag leave');
        },
        shot(event) {
            console.log('shot on', this.coordinates);
            this.isHit = true;
            event.target.classList.add('board-cell-disable');
        },
    },
    created() {
        let center = Math.floor(this.coordinates.length / 2);
        let char = parseInt(this.coordinates.substring(0, center)) + 64;
        let number = this.coordinates.substring(center);

        if (number.length === 2 && number[0] === '0') {
            char += 9;
            number = number.substring(1);
        }

        this.coordinates = String.fromCharCode(char) + number;
    },
}
</script>

<style scoped>

</style>