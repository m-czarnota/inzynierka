<template>
    <ships-storage></ships-storage>
    <board></board>
</template>

<script>
import BoardComponent from "./BoardComponent";
import ShipComponent from "./ShipComponent";
import {gameState} from "./GameState";
import ShipsStorage from "./ShipsStorageComponent";

export default {
    name: "ArrangeShips",
    components: {ShipsStorage, ShipComponent, BoardComponent},
    data() {

    },
}
</script>

<style scoped>

</style>