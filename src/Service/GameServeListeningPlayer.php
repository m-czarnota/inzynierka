<?php

namespace App\Service;

use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\Enums\KindOfGameEnum;
use App\Entity\Game;
use App\Entity\User;

class GameServeListeningPlayer extends GameServePlayer
{
//    private GameServeAiAction $gameServeAiAction;
//
//    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator, GameServeAiAction $gameServeAiAction)
//    {
//        parent::__construct($em, $security, $translator);
//        $this->gameServeAiAction = $gameServeAiAction;
//    }

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
        /** @var Game $game */
        $game = $this->security->getUser()->getGame();
        if (in_array($game->getKindOfGame(), [KindOfGameEnum::GAME_AI, KindOfGameEnum::GAME_AI_RANKED])) {
//            $this->gameServeAiAction->serveAction();
        }

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