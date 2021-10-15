<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GameServeListeningPlayer
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
            'positionInGameInfo' => 5,
            'isReading' => 0,
            'mishits' => ['A2', 'F5', 'C6'],
        ];
    }

    public function serveAction(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        if (!$game) {
            return [
                'status' => 'error',
                'message' => 'You are not in a game!',
            ];
            // Response::HTTP_BAD_REQUEST
        }

        if (count($game->getGameInfo()) === count($game->getUsers())) {
            return [
                'status' => 'no_changed',
                'message' => "Waiting for action",
            ];
        }

        return $this->serveWaitingLastAction();
    }

    private function serveWaitingLastAction()
    {
        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction || $lastAction['isReading']) {
            // waiting for action
        }

        $hitShipId = $this->getHitShipIdByCoordinatesFromLastAction();
        if (!$hitShipId) {
            // mishit
        }

        if ($this->isShipKilledInAction($lastAction, $hitShipId)) {
            // ship killed
        }

        // ship hit


        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $lastAction['isReading'] = true;
        $gameInfo = $game->getGameInfo();
        $gameInfo[$lastAction['positionInGameInfo']] = $lastAction;
        $game->setGameInfo($gameInfo);

        $this->em->persist($game);
        $this->em->flush();

        if ($endGameData = $this->getEndGameData()) {
            return $endGameData;
        }
    }

    private function getLastOpponentAction(bool $findForUser = false): ?array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();
        $gameInfo = $game->getGameInfo();

        if (count($gameInfo) === count($game->getUsers())) {
            return null;
        }

        $actions = [];
        $searchedOpponent = $game->getUsers()[$this->getUserPositionInQueue(!$findForUser)];
        foreach ($gameInfo as $index => $action) {
            if (in_array($index, range(0, 1)) || $action['userAction'] === $searchedOpponent) {
                continue;
            }

            $actions[] = $action;
        }

        if (empty($actions)) {
            return null;
        }

        return $actions[count($actions) - 1];
    }

    private function isGameOver(): bool
    {
        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo(true));
    }

    private function isVictory(): bool
    {
        $lastAction = $this->getLastOpponentAction(true);
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo());
    }

    private function getUserPositionInQueue(bool $searchOpponent = false): int
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = array_search($user, $game->getUsers());
        return $searchOpponent ? (int)!$userPositionInQueue : $userPositionInQueue;
    }

    private function getUserShipsInfo(bool $getOpponentInfo = false): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = $this->getUserPositionInQueue($getOpponentInfo);
        return $game->getGameInfo()[$userPositionInQueue];
    }

    private function getEndGameData(): ?array
    {
        if ($this->isGameOver()) {
            // game over
            return [];
        }

        if ($this->isVictory()) {
            // victory
            return [];
        }

        return null;
    }

    private function getHitShipIdByCoordinatesFromLastAction(): ?int
    {
        $lastAction = $this->getLastOpponentAction();

        foreach ($this->getUserShipsInfo() as $ship) {
            if (in_array($ship->id, $lastAction['killed'])) {
                continue;
            }

            foreach ($ship->boardFields as $boardField) {
                if ($boardField->coordinates === $lastAction['coordinates']) {
                    return $ship->id;
                }
            }
        }

        return null;
    }

    private function isShipKilledInAction($action, int $shipId): bool
    {
        $hitShips = $action['hit'][$shipId];
        return count($hitShips['hit']) === $hitShips['elementsCount'];
    }
}