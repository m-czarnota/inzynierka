<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class GameServePlayer
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;

        $lastActionData = [
            'userAction' => 'user',
            'status' => 'shoot/hit/shoot_down',
            'coordinates' => 'A1',
            'hit' => [
                4 => [
                    'elementsCount' => 3,
                    'hit' => [1, 2],
                ],
                2 => [
                    'elementsCount' => 2,
                    'hit' => [2],
                ],
            ],
            'killed' => [9, 4],
        ];
    }

    public function serveAction()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = array_search($user, $game->getUsers());
        $isUserTurn = $userPositionInQueue === $game->getPlayerTurn();

        if (!$game) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You are not in a game!',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$isUserTurn) {
            return new JsonResponse([
                'status' => 'no_changed',
                'message' => "Waiting for action",
                'isUserTurn' => $isUserTurn,
            ]);
        }

        if (count($game->getGameInfo()) === count($game->getUsers())) {
            $this->changeTurn();

            return new JsonResponse([
                'status' => 'no_changed',
                'message' => "Waiting for action",
            ]);
        }

        $this->serveLastAction();
    }

    private function changeTurn()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = array_search($user, $game->getUsers());

        $gameInfo = $game->getGameInfo();
        array_push($gameInfo, []);

        $game->setGameInfo($gameInfo);
        $game->setPlayerTurn($userPositionInQueue);

        $this->em->persist($game);
        $this->em->flush();
    }

    private function serveLastAction()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();
        $gameInfo = $game->getGameInfo();
        $lastAction = $gameInfo[count($gameInfo) - 1];

        if (empty($lastAction)) {
            // waiting for action
        }

        $response = [
            'status' => $lastAction->status,
            'data' => null,
        ];

        if (!in_array($lastAction->action, [])) {

        }

        if (count($lastAction->killed) === count($game->getGameInfo()[array_search($user, $game->getUsers())])) {
            // game over
        }

        return new JsonResponse([
            'status' => 'change_turn',
            'data' => $lastAction,
        ]);

    }
}