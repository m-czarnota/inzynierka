import {board} from "../entities/game/Board";

class GameState {
    constructor() {
        this.isActiveGame = false;
        this.kindOfGame = null;
    }
}

export const gameState = new GameState();