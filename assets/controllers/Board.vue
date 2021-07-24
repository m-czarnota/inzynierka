<template>
    <div class="board">
        <div v-for="n in size" class="board-row">
            <field v-for="k in size" :coordinatesProp="n + '' + k"></field>
        </div>
    </div>
</template>

<script>
import Field from "./Field";
export default {
    name: "Board",
    components: {Field},
    data() {
        return {
            size: 10,
            fields: [],
        };
    },
    methods: {

    },
    created() {
        console.log('stworzono');
    },
    mounted() {
        console.log('dupa');
    }
}
</script>

<style scoped>

</style>