<?php

namespace App\Service;

use App\Entity\Enums\GameResponseStatusEnum;
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
                'status' => GameResponseStatusEnum::ERROR,
                'message' => $this->translator->trans('game.gameActions.responses.notInGame'),
            ];
            // Response::HTTP_BAD_REQUEST
        }

        return $this->serveWaitingLastAction();
    }

    private function serveWaitingLastAction(): array
    {
        $dataToReturn = [
            'status' => GameResponseStatusEnum::NO_CHANGED,
            'message' => $this->translator->trans('game.gameActions.responses.waiting'),
        ];

        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction || $lastAction['isReading']) {
            return $dataToReturn;
        }

        $hitShipId = $this->getHitShipIdByCoordinatesFromLastAction();
        if (!$hitShipId) {
            $dataToReturn['status'] = GameResponseStatusEnum::MISS_HIT;
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.miss_hit');
            return $dataToReturn;
        }

        if ($this->isShipKilledInAction($lastAction, $hitShipId)) {
            $dataToReturn['status'] = GameResponseStatusEnum::KILLED;
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.killed');
            return $dataToReturn;
        }

        $dataToReturn['status'] = GameResponseStatusEnum::HIT;
        $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.hit');

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

        return $dataToReturn;
    }

    private function getHitShipIdByCoordinatesFromLastAction(): ?int
    {
        $lastAction = $this->getLastOpponentAction();

        foreach ($this->getUserShipsInfo() as $ship) {
            if ($lastAction !== null || in_array($ship['id'], $lastAction['killed'])) {
                continue;
            }

            foreach ($ship['boardFields'] as $boardField) {
                if ($boardField['coordinates'] === $lastAction['coordinates']) {
                    return $ship['id'];
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