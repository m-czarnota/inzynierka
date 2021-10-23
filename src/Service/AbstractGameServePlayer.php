<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractGameServePlayer
{
    protected EntityManagerInterface $em;
    protected Security $security;
    protected TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;

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

    protected function getUserPositionInQueue(bool $searchOpponent = false): int
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = array_search($user, $game->getUsers()->getValues());
        return $searchOpponent ? (int)!$userPositionInQueue : $userPositionInQueue;
    }

    protected function getLastOpponentAction(bool $findForUser = false): ?array
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
            if (in_array($index, range(0, count($game->getUsers()) - 1)) || $action['userAction'] === $searchedOpponent->getId()) {
                continue;
            }

            $actions[] = $action;
        }

        if (empty($actions)) {
            return null;
        }

        return $actions[count($actions) - 1];
    }

    protected function isGameOver(): bool
    {
        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo(true));
    }

    protected function isVictory(): bool
    {
        $lastAction = $this->getLastOpponentAction(true);
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo());
    }

    protected function getUserShipsInfo(bool $getOpponentInfo = false): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $userPositionInQueue = $this->getUserPositionInQueue($getOpponentInfo);
        return $game->getGameInfo()[$userPositionInQueue];
    }

    protected function getEndGameData(): ?array
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

    protected function generateEmptyLastAction(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        return [
            'userAction' => $user->getId(),
            'status' => null,
            'coordinates' => null,
            'hit' => [],
            'killed' => [],
            'positionInGameInfo' => count($game->getGameInfo()) + 1,
            'isReading' => 0,
            'mishits' => [],
        ];
    }

    protected function findShipByIdInUserShips(int $shipId, bool $forOpponent = false): ?array
    {
        $userShipsInfo = $this->getUserShipsInfo($forOpponent);

        foreach ($userShipsInfo as $ship) {
            if ($ship['id'] === $shipId) {
                return $ship;
            }
        }

        return null;
    }
}