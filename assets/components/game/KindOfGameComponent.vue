<template>
    <div class="kind-of-game-component">
        <div class="kinds-of-game">
            <div v-for="(value, name) in kindsOfGame">
                <label for="kindOfGame_{{ value }}">{{ getProcessedName(name) }}</label>
                <input type="radio" id="kindOfGame_{{ value }}" name="kindOfGame">
            </div>
        </div>
        <button type="button" @click="goToArrangeShips">Arrange Ships</button>
    </div>
</template>

<script>
import {kindsOfGame} from "../../loaders/appGame";

export default {
    name: "KindOfGameComponent",
    data() {
        return {
            kindsOfGame: kindsOfGame,
        };
    },
    methods: {
        getProcessedName(name) {
            const processedName = name.replace('game_', '').split('_').join(' ');
            return processedName.charAt(0).toUpperCase() + processedName.slice(1);
        },
        goToArrangeShips() {
        },
    },
}
</script>

<style scoped>

</style>