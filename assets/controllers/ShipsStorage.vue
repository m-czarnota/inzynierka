<template>
    <div class="ships-storage" @drop="onDrop($event)" @dragover.prevent ref="shipsStorage">
        <ship-component v-if="gameState.shipsToDragging[9] !== null"
                        :ship="gameState.shipsToDragging[9]"></ship-component>
    </div>
</template>

<script>
import ShipComponent from "./ShipComponent";
import {gameState} from "./GameState";
import {dragAndDropHelper} from "./DragAndDropHelper";
import {emitter} from "./Emitter";

export default {
    name: "ShipsStorage",
    components: {ShipComponent},
    data() {
        return {
            gameState: gameState,
        }
    },
    methods: {
        onDrop(event) {
            let dataTransfer = dragAndDropHelper.getDataTransfer(event);
            console.log(this.gameState.shipsToDragging[dataTransfer.shipId]);
            gameState.shipsToDragging[dataTransfer.shipId] = dataTransfer.ship;
            console.log(this.gameState.shipsToDragging[dataTransfer.shipId]);

            //TODO disable restore ship on board
            //TODO $forceUpdate this component
        },
    },
    mounted() {
        emitter.on('drop-complete', isComplete => {
            isComplete ? this.$forceUpdate() : '';
        });
    }
}
</script>

<style scoped>

</style>