<template>
    <div class="arrange-component">
        <main class="arrange-main">
            <ships-storage-component></ships-storage-component>
            <board></board>
        </main>
        <button type="button" class="btn btn-warning router-link-button" @click="">
            <router-link class="link-dark text-decoration-none" :to="{ name: 'Game' }">Play</router-link>
        </button>
    </div>
</template>

<script>
import ShipsStorageComponent from "./ShipsStorageComponent";
import Board from "./BoardComponent";

export default {
    name: "ArrangeComponent",
    components: {Board, ShipsStorageComponent},
    methods: {},
}
</script>

<style scoped>

</style>