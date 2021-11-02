<?php

namespace App\Service;

use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;

class GameServeListeningPlayer extends GameServePlayer
{
    /**
     * @throws \Exception
     */
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
        }

        return $this->serveWaitingLastAction();
    }

    /**
     * @throws \Exception
     */
    private function serveWaitingLastAction(): array
    {
        $dataToReturn = [
            'status' => GameResponseStatusEnum::NO_CHANGED,
            'message' => $this->translator->trans('game.gameActions.responses.waiting'),
            'yourTurn' => $this->isYourTurn(),
            'userAction' => $this->getOpponent()->getId(),
        ];

        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction || $lastAction['isReading'] === true) {
            return $dataToReturn;
        }

        $dataToReturn['coordinates'] = $lastAction['coordinates'];
        $hitShipId = $this->findShipIdByCoordinates($this->getUserShipsInfo(), $lastAction['coordinates']);

        $this->markActionAsRead($lastAction);

        if ($hitShipId === null) {
            $dataToReturn['status'] = GameResponseStatusEnum::MISS_HIT;
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.miss_hit');
            return $dataToReturn;
        }

        if ($this->isShipKilledInAction($lastAction, $hitShipId)) {
            $dataToReturn['status'] = GameResponseStatusEnum::KILLED;
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.killed');

            $shipCoordinates = $this->getCoordinatesForShipById($hitShipId);
            $dataToReturn['boardFields'] = $shipCoordinates['boardFields'];
            $dataToReturn['aroundFields'] = $shipCoordinates['aroundFields'];
            return $dataToReturn;
        }

        $dataToReturn['status'] = GameResponseStatusEnum::HIT;
        $dataToReturn['message'] = $this->translator->trans('game.gameActions.responses.hit');

        if ($endGameData = $this->getEndGameData()) {
            return $endGameData;
        }

        return $dataToReturn;
    }

    private function isShipKilledInAction($action, int $shipId): bool
    {
        return in_array($shipId, $action['killed']);
    }
}