<?php

namespace App\Controller\Game;

use App\Entity\Enums\GameRequestStatusEnum;
use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\Enums\KindOfGameEnum;
use App\Entity\User;
use App\Service\GameServeActionPlayer;
use App\Service\GameServeListeningPlayer;
use App\Service\GameServePlayer;
use App\Service\MatchmakingEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/game", name="app_game_")
 */
class GameController extends AbstractController
{
    /**
     * @Route (path="/prepare_game", methods={"POST"}, name="prepare_game")
     * @param MatchmakingEngine $matchmakingEngine
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function prepareGameAction(MatchmakingEngine $matchmakingEngine, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getGame()) {
            return new JsonResponse([
                'linkToRoom' => $user->getGameRoom()->getLink(),
            ]);
        }

        $kindOfGame = $request->get('kindOfGame');
        if (KindOfGameEnum::isValid($kindOfGame) === false) {
            return new JsonResponse([
                'state' => 'exception',
                'header' => 'Server problem',
                'message' => 'Sorry, matchmaking is not available. Please try again in a few moments.',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $opponent = $matchmakingEngine->searchOpponent($user, [
            'kindOfGame' => $kindOfGame,
            'ships' => $request->get('playerShips'),
            'aiShips' => $request->get('aiShips'),
        ], $request->get('whichApproach'));
        if (!$opponent) {
            return new JsonResponse([
                'state' => GameResponseStatusEnum::ERROR,
                'header' => 'Game not found',
                'message' => 'Sorry, game has not been found.',
            ], Response::HTTP_ACCEPTED);
        }

        $parameters = $matchmakingEngine->saveFoundUsers($user, $opponent);
        $parameters['state'] = 'ok';

        return new JsonResponse($parameters);
    }

    /**
     * @Route (path="/isUserInGame", name="is_user_in_game")
     */
    public function checkIfIsUserInGameAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $isUserInGame = (bool)$user->getGame();

        return new JsonResponse([
            'status' => $isUserInGame,
            'linkToRoom' => $isUserInGame ? $user->getGameRoom()->getLink() : null,
        ]);
    }

    /**
     * @Route(path="/getUserShips", name="get_user_ships")
     * @throws \Exception
     */
    public function getUserShipsAction(GameServePlayer $gameServePlayer): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $game = $user->getGame();

        $turnFlag = $gameServePlayer->getUserPositionInQueue();
        $gameInfo = $user->getGame()->getGameInfo();

        // remove opponent ships from game info and get user ships
        array_splice($gameInfo, (int)!$turnFlag, 1);
        $userShips = array_shift($gameInfo)['ships'];

        foreach ($gameInfo as &$info) {
            if ($info['status'] !== GameResponseStatusEnum::KILLED || $info['userAction'] !== $user->getId()) {
                continue;
            }

            $killedShipsIds = $info['killed'];
            $shipCoordinates = $gameServePlayer->getCoordinatesForShipById($killedShipsIds[count($killedShipsIds) - 1], true);
            $info['boardFields'] = $shipCoordinates['boardFields'];
            $info['aroundFields'] = $shipCoordinates['aroundFields'];
        }

        return new JsonResponse([
            'ships' => $userShips,
            'actions' => $gameInfo,
            'turnFlag' => $turnFlag,
            'yourTurn' => $game->getPlayerTurn() === $turnFlag,
            'yourId' => $user->getId(),
        ]);
    }

    /**
     * @Route (path="/serveListeningPlayer", name="serve_listening_player")
     * @throws \Exception
     */
    public function serveListeningPlayerAction(Request $request, GameServeListeningPlayer $serveListeningPlayer): JsonResponse
    {
        $data = $serveListeningPlayer->serveAction();

        return new JsonResponse(
            $data,
            $data['status'] !== GameResponseStatusEnum::ERROR ? Response::HTTP_OK : Response::HTTP_CONFLICT
        );
    }

    /**
     * @Route (path="/servePlayerMove", name="serve_player_move", methods={"POST"})
     * @throws \Exception
     */
    public function servePlayerMoveAction(Request $request, GameServeActionPlayer $serveActionPlayer): JsonResponse
    {
        $dataFromPlayer = [
            'action' => $request->get('action'),
            'coordinates' => $request->get('coordinates'),
        ];
        $data = $serveActionPlayer->serveAction($dataFromPlayer);

        return new JsonResponse(
            $data,
            $data['status'] !== GameResponseStatusEnum::ERROR ? Response::HTTP_OK : Response::HTTP_CONFLICT
        );
    }

    /**
     * @Route (path="/{optional}", defaults={"optional"=""}, options={"expose"=true}, name="arrange")
     */
    public function arrangeAction(): Response
    {
        return $this->render('game/base.html.twig', [
            'kindsOfGame' => base64_encode(json_encode(KindOfGameEnum::serialize())),
            'responseStatuses' => base64_encode(json_encode(GameResponseStatusEnum::serialise())),
            'requestStatuses' => base64_encode(json_encode(GameRequestStatusEnum::serialise())),
        ]);
    }
}