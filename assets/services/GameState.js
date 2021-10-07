import Cookies from "js-cookie";
import {shipsStorage} from "../entities/game/ShipsStorage";
import {board} from "../entities/game/Board";
import {shipPlacementService} from "./ShipPlacementService";

class GameState {
    constructor() {
        this.isActiveGame = false;
        this.kindOfGame = null;

        this.loadFromCookies();
    }

    loadFromCookies() {
        if (!Cookies.get('gameInfo')) {
            return;
        }

        const gameInfo = JSON.parse(Cookies.get('gameInfo'));
        this.kindOfGame = gameInfo.kindOfGame;
        // board.ships = JSON.parse(gameInfo.board.ships);
        // shipsStorage.ships = JSON.parse(gameInfo.shipsStorage.ships);

        // renew Cookie life
        this.saveInfoToCookies();
    }

    saveInfoToCookies() {
        Cookies.set('gameInfo', JSON.stringify({
            kindOfGame: this.kindOfGame,
            board: {
                ships: shipPlacementService.stringifyShips(board.ships),
            },
            shipsStorage: {
                ships: shipPlacementService.stringifyShips(shipsStorage.ships),
            }
        }, {
            expires: 1,
        }));
    }
}

export const gameState = new GameState();