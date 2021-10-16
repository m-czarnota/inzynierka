<?php

namespace App\Service;

use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;
use http\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GameServeActionPlayer extends AbstractGameServePlayer
{
    public function serveAction(array $data): array
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

        if (!in_array($data['action'], [GameResponseStatusEnum::SHOT, GameResponseStatusEnum::MISSED_TURN])) {
            throw new BadRequestHttpException("Wrong player's action passed to controller's action");
        }

        switch ($data['action']) {
            case GameResponseStatusEnum::MISSED_TURN:
                return $this->serveMissedTurn($data);
            case GameResponseStatusEnum::SHOT:
                return $this->serveShot($data);
            default:
                throw new RuntimeException("Player action's is wrong.");
        }
    }

    private function serveMissedTurn(array $data): array
    {
        $this->changeTurn();
        return [
            'status' => GameResponseStatusEnum::CHANGE_TURN,
            'message' => '',
        ];
    }

    private function serveShot(array $data): array
    {
        $lastAction = $this->getLastOpponentAction(true);
        if ($lastAction === null || !empty($lastAction)) {
            $lastAction = $this->generateEmptyLastAction();
        }

        return [
            'status' => '',
            'message' => '',
        ];
    }

    private function changeTurn()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $gameInfo = $game->getGameInfo();
        array_push($gameInfo, []);

        $game->setGameInfo($gameInfo);
        $game->setPlayerTurn($this->getUserPositionInQueue());

        $this->em->persist($game);
        $this->em->flush();
    }
}