<template>
    <ship-component :ship="ships[8]"></ship-component>
    <ship-component :ship="ships[9]" v-if="isChecked"
                    v-show="isChecked" @click="dupa()"></ship-component>
    <board></board>
    {{ isChecked }}
</template>

<script>
import Board from "./Board";
import ShipComponent from "./ShipComponent";
import {gameState} from "./GameState";

export default {
    name: "ArrangeShips",
    components: {ShipComponent, Board},
    data() {
        return {
            ships: gameState.shipsToDragging
        }
    },
    mounted() {
        // setInterval(() => {
        //     console.log(this.ships.length);
        //     if (this.ships[9] === undefined) {
        //         console.log('robie update');
        //         this.$forceUpdate();
        //     }
        // }, 1000);
    },
    methods: {
        dupa() {
            this.ships.pop();
            console.log('dupa click');
        }
    },
    computed: {
        isChecked() {
            console.log('dupa');
            return this.ships.length === 10;
        }
    },
    watch: {
        ships: {
            handler(val, oldVal) {
                console.log('changed');
            },
            deep: true
        }
    }
}
</script>

<style scoped>

</style>