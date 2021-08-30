<template>
    <ships-storage></ships-storage>
    <board></board>
</template>

<script>
import Board from "./Board";
import ShipComponent from "./ShipComponent";
import {gameState} from "./GameState";
import ShipsStorage from "./ShipsStorage";

export default {
    name: "ArrangeShips",
    components: {ShipsStorage, ShipComponent, Board},
    data() {
        return {
            ships: gameState.shipsToDragging
        }
    },
}
</script>

<style scoped>

</style>