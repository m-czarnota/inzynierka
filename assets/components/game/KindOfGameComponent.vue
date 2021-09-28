<template>
    <div class="kind-of-game-component">
        <div class="kind-of-game-choices">
            <div v-for="(value, name) in kindsOfGame" class="choice-div">
                <input type="radio" :id="`kindOfGame${value}`" :value="value" name="kindOfGame" v-model="chosenOption">
                <label :for="`kindOfGame${value}`">{{ getProcessedName(name) }}</label>
            </div>
        </div>
        <div class="buttons d-flex justify-content-end">
            <button class="btn btn-warning" type="button" @click="goToArrangeShips">Arrange Ships</button>
        </div>
    </div>
</template>

<script>
import {kindsOfGame} from "../../loaders/appGame";
import {gameState} from "../../services/GameState";

export default {
    name: "KindOfGameComponent",
    data() {
        return {
            kindsOfGame: kindsOfGame,
        };
    },
    methods: {
        capitalize(word) {
            word = word.charAt(0).toUpperCase() + word.slice(1);
            return word === 'Ai' ? word.toUpperCase() : word;
        },
        getProcessedName(name) {
            const processedName = name.replace('game_', '').split('_').join(' ');
            return processedName.split(' ').map(word => this.capitalize(word)).join(' ');
        },
        goToArrangeShips() {
            if (!Object.values(this.kindsOfGame).includes(this.chosenOption)) {
                alert('Something went wrong. Please try again.');
                return;
            }

            gameState.kindOfGame = this.chosenOption;
            this.$router.replace({name: 'Arrange Ships'});
        },
    },
    setup() {
        // TODO remember state and load it in case of reload page

        return {
            chosenOption: gameState.kindOfGame ?? null
        }
    },
}
</script>

<style scoped>

</style>