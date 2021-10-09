import {shipsStorage} from "../entities/game/ShipsStorage";
import {board} from "../entities/game/Board";
import {shipPlacementService} from "./ShipPlacementService";

class GameState {
    constructor() {
        this.isActiveGame = false;
        this.kindOfGame = null;
        this.gameInfoStorageKey = 'gameInfo';

        this.loadFromStorage();
    }

    loadFromStorage() {
        const gameInfoStorage = localStorage.getItem(this.gameInfoStorageKey);
        if (!gameInfoStorage) {
            return;
        }

        const gameInfo = JSON.parse(gameInfoStorage);
        console.log(gameInfo);
        console.log(JSON.parse(gameInfo.board.ships));
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
}

export const gameState = new GameState();