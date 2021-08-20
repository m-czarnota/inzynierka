<template>
    <ship-component v-if="ships[8] !== null" :ship="ships[8]"></ship-component>
    <ship-component v-if="ships[9] !== null" :ship="ships[9]"></ship-component>
    <board></board>
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
}
</script>

<style scoped>

</style>