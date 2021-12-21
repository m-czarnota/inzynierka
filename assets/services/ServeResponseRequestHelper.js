import {gameState} from "./GameState";
import {responseStatuses} from "../loaders/appGame";
import {emitter} from "./Emitter";

class ServeResponseRequestHelper {
    serveAction(data, board) {
        this.board = board;
        this.data = data;

        emitter.emit('updateInfoBanner', (() => {
            let message = this.isUserOwner() ? 'You: ' : 'Opponent: ';
            return message + this.data.header;
        })());

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

        if (gameState.displayMessages && !gameState.applyingMovesAfterLoad && !this.getNoDisplayMessageActions().includes(this.data.status)) {
            emitter.emit('newBasicToast', {
                header: this.data.header,
                message: this.data.message,
                time: this.getCurrentTimeWithoutSeconds(),
            });
        }

        switch (this.data.status) {
            case responseStatuses.error:
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
        const isUserOwner = this.isUserOwner();
        const killedShipToEmit = {
            'shipId': this.data.killed.at(-1),
            'isUserOwner': isUserOwner,
        }

        if (isUserOwner) {
            const field = this.board.getFieldByCoordinates(this.data.coordinates);
            const ship = this.board.ships.find(ship => ship.id === field.shipPointer);
            ship.setKilledStatus();

            emitter.emit('updateShipInfo', killedShipToEmit);

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

        emitter.emit('updateShipInfo', killedShipToEmit);
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

    getCurrentTime() {
        const currentDate = new Date().toLocaleString();
        const posOfComma = currentDate.indexOf(',');

        return currentDate.slice(posOfComma + 2);
    }

    getCurrentTimeWithoutSeconds() {
        const currentTime = this.getCurrentTime();
        return currentTime.slice(0, currentTime.indexOf(':', 4));
    }

    getNoDisplayMessageActions() {
        if (this.noDisplayMessageActions === undefined) {
            this.noDisplayMessageActions = [responseStatuses.no_changed];
        }

        return this.noDisplayMessageActions;
    }

    isUserOwner() {
        return this.data.userAction !== gameState.yourId;
    }
}

export const serveResponseRequestHelper = new ServeResponseRequestHelper();