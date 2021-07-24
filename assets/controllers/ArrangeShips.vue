<template>
    <ship :elementsCountProp="4"></ship>
    <board></board>
</template>

<script>
import Board from "./Board";
import Ship from "./Ship";
export default {
    name: "ArrangeShips",
    components: {Ship, Board}
}
</script>

<style scoped>

</style>