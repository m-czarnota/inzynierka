<template>
    <div class="board-cell" ref="board-cell"></div>
</template>

<script>
let id = 0

export default {
    name: "FieldComponent",
    data() {
        return {
            id: id++,
        };
    },
    methods: {},
}
</script>

<style scoped>

</style>