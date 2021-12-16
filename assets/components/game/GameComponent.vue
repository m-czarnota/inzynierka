<template>
    <div class="game-component d-none">
        <div class="game-boards d-flex flex-wrap-reverse p-3">
            <div class="player col-12 col-md-6 d-flex flex-column flex-xl-row justify-content-evenly align-items-center p-3"
                 id="user"
            >
                <board-component :board="boardUser" :is-user-owner="true"></board-component>
                <ships-info-component :is-user-owner="true"></ships-info-component>
            </div>
            <div class="player col-12 col-md-6 d-flex flex-column flex-xl-row-reverse justify-content-evenly align-items-center p-3"
                 id="opponent"
            >
                <board-component :board="boardOpponent" :disable="gameState.yourTurn"></board-component>
                <ships-info-component class="align-items-end"></ships-info-component>
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
import {serveResponseRequestHelper} from "../../services/ServeResponseRequestHelper";
import ShipsInfoComponent from "./ShipsInfoComponent";

export default {
    name: "GameComponent",
    components: {ShipsInfoComponent, BoardComponent},
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
            dragDropShipHelper.setAppropriateColorForAllFields(true);

            gameState.yourId = data.yourId;
            applyAllPreviousActions(data.actions, boardUser, boardOpponent);
            gameState.turnFlag = data.turnFlag;
            gameState.setTurn(data.yourTurn);
            document.querySelector('.game-component').classList.remove('d-none');

            listenForResponse(boardUser);
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
    const listener = async () => {
        const response = await fetch(gameRouter.gameRoutes.serveListeningPlayer);
        const data = await response.json();

        if (!response.status) {
            console.error(data.message);
            return;
        }

        serveResponseRequestHelper.serveAction(data, board);
    }

    listener();
    // setInterval(listener, 1000);
};

const applyAllPreviousActions = (actions, boardUser, boardOpponent) => {
    const applyPreviousAction = (action) => {
        if (!action['isReading'] && action['userAction'] !== gameState.yourId) {
            return;
        }

        action['userAction'] === gameState.yourId ?
            serveResponseRequestHelper.serveAction(action, boardOpponent) :
            serveResponseRequestHelper.serveAction(action, boardUser);
    }

    gameState.applyingMovesAfterLoad = true;
    actions.forEach(action => applyPreviousAction(action));
    gameState.applyingMovesAfterLoad = false;
}
</script>

<style scoped>

</style>