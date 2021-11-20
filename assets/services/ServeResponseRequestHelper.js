import {gameState} from "./GameState";
import {responseStatuses} from "../loaders/appGame";

class ServeResponseRequestHelper {
    serveAction(data, board) {
        this.board = board;
        this.data = data;

        if (data.status === responseStatuses.end_game) {
            this.serveBasicActions();
            this.serveEndGame();
            return;
        }

        if (data.status === responseStatuses.walkover) {
            this.serveWalkover();
            return;
        }

        this.serveBasicActions();
    }

    serveBasicActions() {
        if (!this.data || !this.board) {
            throw new Error('Data or Board is missing!');
        }

        if (gameState.displayMessages && !gameState.applyingMovesAfterLoad) {
            console.log(this.data.message);
        }

        switch (this.data.status) {
            case responseStatuses.error:
                console.error(this.data.message);
                break;
            case responseStatuses.hunted_and_hit:
            case responseStatuses.hit:
                this.serveHit();
                break;
            case responseStatuses.killed:
                this.serveKill();
                break;
            case responseStatuses.hunted:
            case responseStatuses.miss_hit:
                this.serveMissHit();
                break;
        }
    }

    serveHit() {
        const field = this.board.getFieldByCoordinates(this.data.coordinates);
        field.setHitStatus();
    }

    serveKill() {
        if (this.data.userAction !== gameState.yourId) {
            const field = this.board.getFieldByCoordinates(this.data.coordinates);
            const ship = this.board.ships.find(ship => ship.id === field.shipPointer);
            ship.setKilledStatus();
            return;
        }

        this.data.boardFields.forEach(boardFieldCoordinates => {
            const field = this.board.getFieldByCoordinates(boardFieldCoordinates);
            field.setKilledStatus();
        });
        this.data.aroundFields.forEach(aroundFieldCoordinates => {
            const field = this.board.getFieldByCoordinates(aroundFieldCoordinates);
            field.setInactiveStatus();
        });
    }

    serveMissHit() {
        const field = this.board.getFieldByCoordinates(this.data.coordinates);
        field.setMisHitStatus();

        gameState.changeTurn(this.data.yourTurn);
    }

    serveEndGame() {
        alert('end game!');
    }

    serveWalkover() {
        alert('walkover, end game');
    }
}

export const serveResponseRequestHelper = new ServeResponseRequestHelper();