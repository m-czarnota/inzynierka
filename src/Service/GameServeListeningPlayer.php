<?php

namespace App\Service;

use App\Entity\User;

class GameServeListeningPlayer extends AbstractGameServePlayer
{
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

    private function getHitShipIdByCoordinatesFromLastAction(): ?int
    {
        $lastAction = $this->getLastOpponentAction();

        foreach ($this->getUserShipsInfo() as $ship) {
            if ($lastAction !== null || in_array($ship->id, $lastAction['killed'])) {
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