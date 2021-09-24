<template>
    <h1 v-if="!gameState.isActiveGame">Oops! Route not found!</h1>
    <h1 v-else>You are currently playing a match.
        <router-link :to="{ name: 'Match' }">Go back and win!</router-link>
    </h1>
</template>

<script>
import {gameState} from "../../services/GameState";

export default {
    name: "NotFoundComponent",
    data() {
        return {
            gameState: gameState,
        };
    }
}
</script>

<style scoped>

</style>