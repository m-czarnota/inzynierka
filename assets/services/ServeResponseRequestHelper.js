class ServeResponseRequestHelper {
    serveHitRequest(data, board, lastShotCoordinates) {
        console.log(data.message);
        const hitField = board.getFieldByCoordinates(lastShotCoordinates);

        hitField.setHitStatus();
    }

    serveHitResponse(data, board) {
        console.log(data.message)
    }

    serveKillRequest(data, board, lastShotCoordinates) {
        console.log(data.message);
        const hitField = board.getFieldByCoordinates(lastShotCoordinates);
        const ship = board.ships.find(ship => ship === hitField.shipPointer);

        ship.setKilledStatus();
    }

    serveKillResponse(data, board) {
        console.log(data.message)
    }

    serveMissHitRequest(data) {
        console.log(data.message);
    }

    serveMissHitResponse(data) {
        console.log(data.message)
    }

    serveEndGame(data) {
        console.log(data.message);
        alert('end game!');
    }

    serveWalkover(data) {
        console.log(data.message);
        alert('walkover, end game');
    }
}

export const serveResponseRequestHelper = new ServeResponseRequestHelper();