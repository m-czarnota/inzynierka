<?php

namespace App\Controller\Game;

use App\Entity\Enums\KindOfGameEnum;
use App\Entity\User;
use App\Service\MatchmakingEngine;
use Doctrine\ORM\EntityManagerInterface;
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
                'state' => 'error',
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
    public function getUserShipsAction(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $game = $user->getGame();

        $turnFlag = array_search($user, $game->getUsers());
        $gameShips = $user->getGame()->getGameInfo();
        $userShips = $gameShips[$turnFlag];

        return new JsonResponse([
            'ships' => $userShips,
            'turnFlag' => $turnFlag,
            'playerTurn' => $turnFlag === $game->getPlayerTurn(),
        ]);
    }

    public function servePlayerInGameAction(EntityManagerInterface $em, Request $request): JsonResponse
    {


        return new JsonResponse([
            'status' => 'no_changed',
            'message' => "Waiting for action",
        ]);
    }

    /**
     * @Route (path="/{optional}", defaults={"optional"=""}, options={"expose"=true}, name="arrange")
     */
    public function arrangeAction(): Response
    {
        return $this->render('game/base.html.twig', [
            'kindsOfGame' => base64_encode(json_encode(KindOfGameEnum::serialize())),
        ]);
    }
}