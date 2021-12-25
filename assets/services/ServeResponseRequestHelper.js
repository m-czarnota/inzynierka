import {gameState} from "./GameState";
import {responseStatuses} from "../loaders/appGame";
import {emitter} from "./Emitter";
import {timeUtil} from "../utils/TimeUtil";

import $ from "jquery";

class ServeResponseRequestHelper {
    serveAction(data, board) {
        this.board = board;
        this.data = data;

        emitter.emit('updateInfoBanner', (() => {
            let message = this.isUserOwner() ? 'You: ' : 'Opponent: ';
            return message + this.data.header;
        })());

        if (data.status === responseStatuses.end_game) {
            emitter.emit('updateInfoBanner', (() => {
                let message = this.isUserOwner() ? 'You: ' : 'Opponent: ';
                return message + data.basicData.header;
            })());

            this.serveBasicActions(data.basicData);
            this.serveEndGame();
            return;
        }

        if (data.status === responseStatuses.walkover) {
            this.serveWalkover();
            return;
        }

        this.serveBasicActions();
    }

    serveBasicActions(data = undefined) {
        data = data || this.data;

        if (!data || !this.board) {
            throw new Error('Data or Board is missing!');
        }

        if (gameState.displayMessages && !gameState.applyingMovesAfterLoad && !this.getNoDisplayMessageActions().includes(data.status)) {
            emitter.emit('newBasicToast', {
                header: data.header,
                message: data.message,
                time: timeUtil.getCurrentTimeWithoutSeconds(),
            });
        }

        switch (data.status) {
            case responseStatuses.error:
                break;
            case responseStatuses.hunted_and_hit:
            case responseStatuses.hit:
                this.serveHit(data);
                break;
            case responseStatuses.killed:
                this.serveKill(data);
                break;
            case responseStatuses.hunted:
            case responseStatuses.miss_hit:
                this.serveMissHit(data);
                break;
        }
    }

    serveHit(data) {
        const field = this.board.getFieldByCoordinates(data.coordinates);
        field.setHitStatus();
    }

    serveKill(data) {
        const isUserOwner = this.isUserOwner();
        const killedShipToEmit = {
            'shipId': data.killed.at(-1),
            'isUserOwner': isUserOwner,
        }

        if (isUserOwner) {
            const field = this.board.getFieldByCoordinates(data.coordinates);
            const ship = this.board.ships.find(ship => ship.id === field.shipPointer);
            ship.setKilledStatus();

            emitter.emit('updateShipInfo', killedShipToEmit);

            return;
        }

        data.boardFields.forEach(boardFieldCoordinates => {
            const field = this.board.getFieldByCoordinates(boardFieldCoordinates);
            field.setKilledStatus();
        });
        data.aroundFields.forEach(aroundFieldCoordinates => {
            const field = this.board.getFieldByCoordinates(aroundFieldCoordinates);
            field.setInactiveStatus();
        });

        emitter.emit('updateShipInfo', killedShipToEmit);
    }

    serveMissHit(data) {
        const field = this.board.getFieldByCoordinates(data.coordinates);
        field.setMisHitStatus();

        gameState.changeTurn(data.yourTurn);
    }

    serveEndGame() {
        $('.end-game-status').text(this.data.victory ? 'You win!' : 'You lose');
        this.data.opponentShips.forEach(ship => ship.boardFields.forEach(boardField => {
            const field = this.board.getFieldByCoordinates(boardField.coordinates);
            field.setHitStatus();
        }));

        $('.end-game-component').removeClass('d-none').css({'display': 'none'}).slideDown('slow');
        emitter.emit('yourTurn', false);
    }

    serveWalkover() {
        alert('walkover, end game');
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