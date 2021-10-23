class ServeResponseRequestHelper {
    serveHitResponse(data, board) {
        console.log(data.message)
    }

    serveKillResponse(data, board) {
        console.log(data.message)
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