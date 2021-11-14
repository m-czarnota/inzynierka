<template>
    <div class="ships-storage-component col-6"
         @drop="onDrop($event)"
         @dragover.prevent ref="shipsStorage">
        <template v-for="ship in ships" :key="ship.id">
            <ship-component :ship="ship"></ship-component>
        </template>
    </div>
</template>

<script>
import ShipComponent from "./ShipComponent";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";
import {emitter} from "../../services/Emitter";
import {shipsStorage} from "../../entities/game/ShipsStorage";

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
            dragDropShipHelper.storageOnDrop(event);
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