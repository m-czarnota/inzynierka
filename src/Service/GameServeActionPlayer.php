<?php

namespace App\Service;

use App\Entity\Enums\GameRequestStatusEnum;
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
                'status' => GameResponseStatusEnum::ERROR,
                'message' => $this->translator->trans('game.gameActions.requests.notInGame'),
            ];
        }

        if (!in_array($data['action'], [GameRequestStatusEnum::SHOT, GameRequestStatusEnum::MISSED_TURN])) {
            throw new BadRequestHttpException("Wrong player's action passed to controller's action");
        }

        switch ($data['action']) {
            case GameRequestStatusEnum::MISSED_TURN:
                return $this->serveMissedTurn($data);
            case GameRequestStatusEnum::SHOT:
                return $this->serveShot($data);
            default:
                throw new RuntimeException("Player action's is wrong.");
        }
    }

    private function serveMissedTurn(array $data): array
    {
        $this->changeTurn();
        return [
            'status' => GameResponseStatusEnum::MISSED_TURN,
            'message' => '',
        ];
    }

    private function serveShot(array $data): array
    {
        $dataToReturn = [
            'status' => GameResponseStatusEnum::MISS_HIT,
            'message' => $this->translator->trans('game.gameActions.requests.miss_hit'),
        ];

        $lastAction = $this->generateEmptyLastAction();
        $lastAction['coordinates'] = $data['coordinates'];

        $opponentShipsInfo = $this->getUserShipsInfo(true);
        $hitShipId = $this->getHitShipId($opponentShipsInfo, $data['coordinates']);

        if ($hitShipId !== null) {
            $isKilledHitShip = $this->isKilledHitShip($hitShipId);
            $status = $isKilledHitShip ? GameResponseStatusEnum::KILLED : GameResponseStatusEnum::HIT;

            $lastAction['status'] = $status;
            $dataToReturn['status'] = $status;
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.requests.' . $isKilledHitShip ? 'killed' : 'hit');

            if ($this->isKilledHitShip($hitShipId)) {
                array_push($lastAction['killed'], $hitShipId);
            }
            $this->addShipToHitInLastAction($lastAction, $hitShipId);
        } else {
            array_push($lastAction['mishits'], $data['coordinates']);
            $lastAction['status'] = GameResponseStatusEnum::MISS_HIT;
        }

        $this->saveLastAction($lastAction);

        if ($endGameData = $this->getEndGameData()) {
            return $endGameData;
        }

        if ($lastAction['status'] === GameResponseStatusEnum::MISS_HIT) {
            $this->changeTurn();
        }

        return $dataToReturn;
    }

    private function getHitShipId(array $ships, string $coordinates): ?int
    {
        $lastOpponentAction = $this->getLastOpponentAction();

        foreach ($ships as $ship) {
            if ($lastOpponentAction !== null && in_array($ship['id'], $lastOpponentAction['killed'])) {
                continue;
            }

            foreach ($ship['boardFields'] as $boardField) {
                if ($boardField['coordinates'] === $coordinates) {
                    return $ship['id'];
                }
            }
        }

        return null;
    }

    private function isKilledHitShip(int $shipId): bool
    {
        $ship = $this->findShipByIdInUserShips($shipId, true);
        if ($ship === null) {
            return false;
        }

        if ($ship['elementsCount'] === 1) {
            return true;
        }

        $lastOpponentAction = $this->getLastOpponentAction();
        if ($lastOpponentAction !== null && array_key_exists($shipId, $lastOpponentAction['hit'])
            && count($lastOpponentAction['hit'][$shipId]['hit']) === $ship['elementsCount'] - 1) {
            return true;
        }

        return false;
    }

    private function changeTurn()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $game->setPlayerTurn((int)!$game->getPlayerTurn());

        $this->em->persist($game);
        $this->em->flush();
    }

    private function saveLastAction(array $lastAction): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $gameInfo = $game->getGameInfo();
        array_push($gameInfo, $lastAction);
        $game->setGameInfo($gameInfo);

        $this->em->persist($game);
        $this->em->flush();
    }

    private function addShipToHitInLastAction(array &$lastAction, int $hitShipId)
    {
        $ship = $this->findShipByIdInUserShips($hitShipId);

        if (array_key_exists($hitShipId, $lastAction['hit'])) {
            array_push($lastAction['hit'][$hitShipId]['hit'], $hitShipId);
            return;
        }

        $lastAction['hit'][$hitShipId] = [
            'elementsCount' => $ship['elementsCount'],
            'hit' => [$this->findNumberOfHitElementInShip($lastAction['coordinates'], $ship)],
        ];
    }

    private function findNumberOfHitElementInShip(string $coordinates, array $ship): int
    {
        foreach ($ship['boardFields'] as $index => $boardField) {
            if ($boardField['coordinates'] === $coordinates) {
                return $index;
            }
        }

        throw new \Exception("Has not found number of hit element in ship {$ship['id']}");
    }
}