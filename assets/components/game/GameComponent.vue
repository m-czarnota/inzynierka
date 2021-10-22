<template>
    <div class="game-component">
        <div class="game-boards d-flex">
            <div class="player col-6" id="user">
                <board-component :board="boardUser" :is-user-owner="true"></board-component>
            </div>
            <div class="player col-6" id="opponent">
                <board-component :board="boardOpponent" :disable="gameState.yourTurn"></board-component>
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
import {gameState} from "../../services/GameState";
import {responseStatuses} from "../../loaders/appGame";
import {serveResponseRequestHelper} from "../../services/ServeResponseRequestHelper";

export default {
    name: "GameComponent",
    components: {BoardComponent},
    data() {
        return {
            gameState: gameState,
        };
    },
    setup() {
        const userShips = ref(null);
        const boardUser = new Board();
        const boardOpponent = new Board();

        onMounted(async () => {
            const response = await fetch(gameRouter.gameRoutes.getUserShips);
            const data = await response.json();

            userShips.value = data.ships;
            userShips.value.forEach(ship => boardUser.ships.push(Ship.createInstanceFromParsedObject(ship, boardUser)));
            dragDropShipHelper.board = boardUser;
            dragDropShipHelper.setAppropriateColorForAllFields();

            gameState.turnFlag = data.turnFlag;
            gameState.changeTurn(data.yourTurn);

            // listenForResponse(boardUser);
        });

        return {
            userShips,
            boardUser,
            boardOpponent,
        };
    },
    methods: {},
}

const listenForResponse = (board) => {
    setInterval(async () => {
        const response = await fetch(gameRouter.gameRoutes.serveListeningPlayer);
        const data = await response.json();

        if (!response.status) {
            console.error(data.message);
            return;
        }

        serveAction(data, board);
    }, 1000);
};

const serveAction = (data, board) => {
    switch (data.status) {
        case responseStatuses.error:
            console.error(data.message);
            break;
        case responseStatuses.hit:
            serveResponseRequestHelper.serveHitResponse(data, board);
            break;
        case responseStatuses.killed:
            serveResponseRequestHelper.serveKillResponse(data, board);
            break;
        case responseStatuses.miss_hit:
            serveResponseRequestHelper.serveMissHitResponse(data);
            gameState.changeTurn();
            break;
        case responseStatuses.end_game:
            serveResponseRequestHelper.serveEndGame(data);
            break;
        case responseStatuses.walkover:
            serveResponseRequestHelper.serveWalkover(data);
            break;
    }
}
</script>

<style scoped>

</style>