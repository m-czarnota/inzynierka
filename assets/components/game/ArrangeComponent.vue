<template>
    <div class="arrange-component">
        <main class="arrange-main">
            <ships-storage-component></ships-storage-component>
            <board></board>
        </main>
        <button type="button" class="btn btn-warning router-link-button" @click="play">Play</button>
    </div>
</template>

<script>
import ShipsStorageComponent from "./ShipsStorageComponent";
import Board from "./BoardComponent";
import {gameRoutes, routeToGame} from "../../loaders/appGame";
import {shipsStorage} from "../../entities/game/ShipsStorage";
import GameComponent from "./GameComponent";
import {gameState} from "../../services/GameState";

export default {
    name: "ArrangeComponent",
    components: {Board, ShipsStorageComponent},
    methods: {
        play() {
            const formData = new FormData();
            formData.append('kindOfGame', '');
            formData.append('playerShips', JSON.stringify(shipsStorage.ships));
            console.log(gameRoutes.prepareGame);

            fetch(gameRoutes.prepareGame, {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(response => {
                    const gameRoomRouteName = 'Match';

                    this.$router.getRoutes().forEach(route => {
                        if (!this.$router.hasRoute(route.name) || route.name === gameRoomRouteName || route.name === 'Not Found') {
                            return;
                        }

                        this.$router.removeRoute(route.name);
                    });

                    this.$router.addRoute({
                        path: `${routeToGame}/${response.linkToRoom}`,
                        name: gameRoomRouteName,
                        component: GameComponent,
                    });
                    this.$router.replace({name: gameRoomRouteName});

                    gameState.isActiveGame = true;
                })
                .catch(error => {
                    console.error(error);
                })
            ;
        }
    },
}
</script>

<style scoped>

</style>