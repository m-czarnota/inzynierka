<?php

namespace App\Controller\Game;

use App\Entity\Enums\GameRequestStatusEnum;
use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\Enums\KindOfGameEnum;
use App\Entity\User;
use App\Service\GameServeActionPlayer;
use App\Service\GameServeListeningPlayer;
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
                'message' => 'Sorry, matchmaking is not available. Please try again in a few moments.',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $opponent = $matchmakingEngine->searchOpponent($user, [
            'kindOfGame' => $kindOfGame,
            'ships' => $request->get('playerShips'),
        ], $request->get('whichApproach'));
        if (!$opponent) {
            return new JsonResponse([
                'state' => GameResponseStatusEnum::ERROR,
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
     */
    public function getUserShipsAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $game = $user->getGame();

        $turnFlag = array_search($user, $game->getUsers()->getValues());
        $gameShips = $user->getGame()->getGameInfo();
        $userShips = $gameShips[$turnFlag];

        return new JsonResponse([
            'ships' => $userShips,
            'turnFlag' => $turnFlag,
            'yourTurn' => $game->getPlayerTurn() === $turnFlag,
            'playerTurn' => $turnFlag === $game->getPlayerTurn(),
        ]);
    }

    /**
     * @Route (path="/serveListeningPlayer", name="serve_listening_player")
     */
    public function serveListeningPlayerAction(Request $request, GameServeListeningPlayer $serveListeningPlayer): JsonResponse
    {
        $data = $serveListeningPlayer->serveAction();

        return new JsonResponse($data, $data['status'] !== GameResponseStatusEnum::ERROR ? Response::HTTP_OK : Response::HTTP_CONFLICT);
    }

    /**
     * @Route (path="/servePlayerMove", name="serve_player_move", methods={"POST"})
     */
    public function servePlayerMoveAction(Request $request, GameServeActionPlayer $serveActionPlayer): JsonResponse
    {
        $dataFromPlayer = [
            'action' => $request->get('action'),
            'coordinates' => $request->get('coordinates'),
        ];
        $data = $serveActionPlayer->serveAction($dataFromPlayer);

        return new JsonResponse($data);
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