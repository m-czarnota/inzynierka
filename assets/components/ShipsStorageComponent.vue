<template>
    <div class="ships-storage-component" @drop="onDrop($event)" @dragover.prevent ref="shipsStorage">
        <template v-for="ship in ships" :key="ship.id">
            <ship-component :ship="ship"></ship-component>
        </template>
    </div>
</template>

<script>
import ShipComponent from "./ShipComponent";
import {dragDropShipHelper} from "../services/DragDropShipHelper";
import {emitter} from "../services/Emitter";
import {board} from "../entities/Board";
import {shipsStorage} from "../entities/ShipsStorage";

export default {
    name: "ShipsStorageComponent",
    components: {ShipComponent},
    data() {
        return {
            ships: shipsStorage.ships
        }
    },
    methods: {
        onDrop(event) {
            if (!event.dataTransfer.getData('dragFromBoard')) {
                return;
            }

            const dataTransfer = dragDropShipHelper.getDataTransfer(event);
            const ship = board.findShipById(dataTransfer.shipId);

            clearTimeout(ship.timerToRestoreShipOnLastPosition);
            ship.actualPoseDecrement();

            shipsStorage.ships.push(ship);
            board.ships.splice(board.ships.indexOf(ship), 1);

            dragDropShipHelper.removeShipHtml(ship);

            setTimeout(() => this.$forceUpdate(), 100);
        },
    },
    mounted() {
        emitter.on('drop-complete', isComplete => {
            isComplete ? this.$forceUpdate() : '';
        });
    },
}
</script>

<style scoped>

</style>