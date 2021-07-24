<template>
    <div class="board-cell" @click.once="shot"></div>
</template>

<script>
export default {
    name: "Field",
    data() {
        return {
            id: 0,
            shipElement: null,
            isHit: false,
            coordinates: this.coordinatesProp,
        };
    },
    props: ['coordinatesProp'],
    methods: {
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