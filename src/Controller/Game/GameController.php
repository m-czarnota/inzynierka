<?php

namespace App\Controller\Game;

use App\Entity\Enums\KindOfGameEnum;
use App\Entity\Game;
use App\Entity\GameRoom;
use App\Entity\User;
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
    public function prepareGame(MatchmakingEngine $matchmakingEngine, Request $request): JsonResponse
    {
        $kindOfGame = $request->get('kindOfGame');
        if (KindOfGameEnum::isValid($kindOfGame) === false) {
            return new JsonResponse([
                'state' => 'exception',
                'message' => 'Sorry, matchmaking is not available. Please try again in a few moments.',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        /** @var User $user */
        $user = $this->getUser();

        $opponent = $matchmakingEngine->searchOpponent($user, [
            'kindOfGame' => $kindOfGame,
            'ships' => $request->get('playerShips'),
        ], $request->get('whichApproach'));
        if (!$opponent) {
            return new JsonResponse([
                'state' => 'error',
                'message' => 'Sorry, game has not been found.',
            ], Response::HTTP_NO_CONTENT);
        }

        $parameters = $matchmakingEngine->saveFoundUsers($user, $opponent);
        $parameters['state'] = 'ok';

        return new JsonResponse($parameters);
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