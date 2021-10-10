<template>
    <div class="kind-of-game-component">
        <div class="kind-of-game-choices">
            <div v-for="(value, name) in kindsOfGame" class="choice-div">
                <input type="radio"
                       :id="`kindOfGame${value}`"
                       :value="value"
                       name="kindOfGame"
                       v-model="chosenOption"
                       @change="saveChoice">
                <label :for="`kindOfGame${value}`">{{ getProcessedName(name) }}</label>
            </div>
        </div>
        <div class="buttons d-flex justify-content-end">
            <button class="btn btn-warning"
                    type="button"
                    @click="goToArrangeShips"
                    :disabled="!choiceIsValid">
                Arrange Ships
            </button>
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
            gameState: gameState,
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
            if (!this.choiceIsValid()) {
                alert('Something went wrong. Please try again.');
                return;
            }

            this.$router.replace({name: 'Arrange Ships'});
        },
        saveChoice() {
            if (!this.choiceIsValid()) {
                return;
            }

            gameState.kindOfGame = this.chosenOption;
            gameState.saveInfoToStorage();
        },
        choiceIsValid() {
            return Object.values(this.kindsOfGame).includes(this.chosenOption);
        },
    },
    setup(props) {
        return {
            chosenOption: gameState.kindOfGame ?? undefined,
        };
    },
}
</script>

<style scoped>

</style>