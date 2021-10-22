import {shipsStorage} from "../entities/game/ShipsStorage";
import {board} from "../entities/game/Board";
import {shipPlacementService} from "./ShipPlacementService";
import {emitter} from "./Emitter";

class GameState {
    constructor() {
        this.isActiveGame = false;
        this.kindOfGame = null;
        this.gameInfoStorageKey = 'gameInfo';

        this.turnFlag = null;
        this.yourTurn = false;

        this.loadFromStorage();
    }

    loadFromStorage() {
        const gameInfoStorage = localStorage.getItem(this.gameInfoStorageKey);
        if (!gameInfoStorage) {
            return;
        }

        const gameInfo = JSON.parse(gameInfoStorage);
        this.kindOfGame = gameInfo.kindOfGame;
        // board.ships = JSON.parse(gameInfo.board.ships);
        // shipsStorage.ships = JSON.parse(gameInfo.shipsStorage.ships);
    }

    saveInfoToStorage() {
        localStorage.setItem(this.gameInfoStorageKey, JSON.stringify({
            kindOfGame: this.kindOfGame,
            board: {
                ships: shipPlacementService.stringifyShips(board.ships),
            },
            shipsStorage: {
                ships: shipPlacementService.stringifyShips(shipsStorage.ships),
            }
        }));
    }

    changeTurn(turnFlag = null) {
        this.yourTurn = turnFlag ?? !this.yourTurn;
        emitter.emit('yourTurn', gameState.yourTurn);
    }
}

export const gameState = new GameState();