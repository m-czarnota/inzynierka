<template>
    <div class="game-component">
        <div class="game-boards d-flex">
            <div class="player col-6" id="user">
                <board-component :board="boardUser"></board-component>
            </div>
            <div class="player col-6" id="opponent">
                <board-component></board-component>
            </div>
        </div>
    </div>
</template>

<script>
import {onMounted, ref} from "vue";
import BoardComponent from "./BoardComponent";
import {gameRouter} from "../../services/GameRouter";
import {Board} from "../../entities/game/Board";
import {Ship} from "../../entities/game/Ship";
import {dragDropShipHelper} from "../../services/DragDropShipHelper";

export default {
    name: "GameComponent",
    components: {BoardComponent},
    setup() {
        const userShips = ref(null);
        const boardUser = new Board();
        const boardOpponent = new Board();

        onMounted(async () => {
            const response = await fetch(gameRouter.gameRoutes.getUserShips);
            userShips.value = await response.json();

            userShips.value.forEach(ship => boardUser.ships.push(Ship.createInstanceFromParsedObject(ship, boardUser)));

            dragDropShipHelper.board = boardUser;
            dragDropShipHelper.setAppropriateColorForAllFields();
        });

        return {
            userShips,
            boardUser,
            boardOpponent,
        };
    },
}
</script>

<style scoped>

</style>