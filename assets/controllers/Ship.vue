<template>
    <div class="board-ship">
        <div v-for="n in elementsCount" class="board-cell board-ship-element"></div>
    </div>
    <button @click="rotate">Rotate</button>
</template>

<script>
import ShipPoses from "./ShipPoses";
const $ = require('jquery');

export default {
    name: "Ship",
    data() {
        return {
            id: 0,
            elementsCount: this.elementsCountProp,
            elements: [],
            poses: [],
            actualPose: 0
        }
    },
    props: ['elementsCountProp'],
    created() {
        this.poses = ShipPoses.data().poses4Elements;
    },
    mounted() {
        let ship = document.querySelector('.board-ship');
        ship.style.display = 'grid';
        ship.style.gridTemplateColumns = '';
        ship.style.gridTemplateRows = '';

        for (let i = 0; i < this.elementsCount; i++) {
            ship.style.gridTemplateColumns += '[column' + (i + 1) + '] 30px ';
            ship.style.gridTemplateRows += '[row' + (i + 1) + '] 30px ';
        }
    },
    methods: {
        rotate() {
            $('.board-ship-element').each((index, element) => {
                $(element).css(this.poses[this.actualPose][index]);
            });
            this.actualPose++;

            if (this.actualPose >= this.poses.length) {
                this.actualPose = 0;
            }
        }
    }
}
</script>

<style scoped>

</style>