import {shipsStorage} from "../entities/game/ShipsStorage";
import {board} from "../entities/game/Board";
import {shipPlacementService} from "./ShipPlacementService";
import {emitter} from "./Emitter";

class GameState {
    constructor() {
        this.isActiveGame = false;
        this.kindOfGame = null;
        this.gameInfoStorageKey = 'gameInfo';
        this.displayMessages = true;
        this.applyingMovesAfterLoad = false;

        this.turnFlag = null;
        this.yourTurn = false;
        this.yourId = null;

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

    setTurn(turnFlag) {
        if (this.applyingMovesAfterLoad) {
            return;
        }

        if (turnFlag === undefined || turnFlag === null) {
            throw new Error('Turn flag must be true or false');
        }

        this.yourTurn = turnFlag;
        emitter.emit('yourTurn', this.yourTurn);
    }

    changeTurn(turnFlag = null) {
        if (this.applyingMovesAfterLoad) {
            return;
        }

        if (turnFlag === this.yourTurn) {
            return;
        }

        this.yourTurn = turnFlag ?? !this.yourTurn;
        emitter.emit('yourTurn', this.yourTurn);
    }
}

export const gameState = new GameState();