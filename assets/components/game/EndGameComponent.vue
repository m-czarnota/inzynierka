<template>
    <div class="end-game-component">
        <div class="end-game-content">
            <div class="end-game-info d-flex flex-column justify-content-center align-items-center mb-3">
                <h3 class="end-game-status">Status</h3>
                <p>Your score: 4253 (<span class="green">+ 423</span>)</p>
                <p>Actual pos in ranking: 15 (<span class="green">+ 4</span>)</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center flex-column flex-sm-row gap-2 end-game-buttons">
                <button type="button" class="btn btn-secondary border-3 shadow-sm d-flex flex-column align-items-center justify-content-center end-game-button" disabled>
                    <span class="p-2 end-game-button-header">Play again!</span>
                    <span>Play with a new player in the same settings.</span>
                </button>
                <button type="button" class="btn btn-secondary border-3 shadow-sm d-flex flex-column align-items-center justify-content-center end-game-button" disabled>
                    <span class="p-2 end-game-button-header">Rematch</span>
                    <span>Play with the same player again.</span>
                </button>
                <button type="button"
                        @click="returnToDashboard"
                        class="btn btn-secondary border-3 shadow-sm d-flex flex-column align-items-center justify-content-center end-game-button">
                    <span class="p-2 end-game-button-header">Return to dashboard</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import {routeToDashboard} from "../../loaders/appGame";

export default {
    name: 'EndGameComponent',
    methods: {
        returnToDashboard() {
            window.location.href = routeToDashboard;
        },
    },
}
</script>