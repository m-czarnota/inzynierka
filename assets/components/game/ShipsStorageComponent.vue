<template>
    <div class="ships-storage-component d-flex p-3">
        <div class="ships-storage d-flex flex-wrap"
             @drop="onDrop($event)"
             @dragover.prevent ref="shipsStorage">
            <template v-for="ship in ships" :key="ship.id">
                <ship-component :ship="ship" class="d-flex flex-column p-1 p-md-2 align-items-center justify-content-center"></ship-component>
            </template>
        </div>
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
        emitter.on('storage-rerender', value => {
            this.$forceUpdate();
        });
    },
}
</script>

<style scoped>

</style>