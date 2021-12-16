<?php

namespace App\Service;

use App\Entity\Enums\GameRequestStatusEnum;
use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameServeActionPlayer extends GameServePlayer
{
    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator, ParameterBagInterface $parameterBag)
    {
        parent::__construct($em, $security, $translator, $parameterBag);
    }

    /**
     * @throws \Exception
     */
    public function serveAction(array $data): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        if (!$game) {
            return [
                'status' => GameResponseStatusEnum::ERROR,
                'header' => $this->translator->trans('game.gameActions.headers.requests.error'),
                'message' => $this->translator->trans('game.gameActions.messages.requests.notInGame'),
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

    /**
     * @throws \Exception
     */
    private function serveMissedTurn(array $data): array
    {
        $this->changeTurn();
        return [
            'status' => GameResponseStatusEnum::MISSED_TURN,
            'header' => '',
            'message' => '',
            'yourTurn' => $this->isYourTurn(),
            'userAction' => !array_key_exists('ai_move', $data) ? $this->security->getUser()->getId() : $this->parameterBag->get('ai_id'),
        ];
    }

    /**
     * @throws \Exception
     */
    private function serveShot(array $data): array
    {
        $dataToReturn = [
            'status' => GameResponseStatusEnum::MISS_HIT,
            'header' => $this->translator->trans('game.gameActions.headers.requests.miss_hit'),
            'message' => $this->translator->trans('game.gameActions.messages.requests.miss_hit'),
            'yourTurn' => $this->isYourTurn(),
            'userAction' => !array_key_exists('ai_move', $data) ? $this->security->getUser()->getId() : $this->parameterBag->get('ai_id'),
            'coordinates' => $data['coordinates'],
        ];

        $userMove = !array_key_exists('ai_move', $data);
        $previousAction = $this->getLastOpponentAction($userMove);

        $lastAction = $previousAction ?? $this->generateEmptyLastAction($userMove);
        $lastAction['isReading'] = false;
        $lastAction['positionInGameInfo'] = count($this->getGameInfo());
        $lastAction['coordinates'] = $data['coordinates'];

        $dataToReturn['userAction'] = $lastAction['userAction'];

        $hitShipId = $this->findShipIdByCoordinates($this->getUserShipsInfo($userMove), $data['coordinates']);

        if ($hitShipId !== null) {
            $isKilledHitShip = $this->isKilledHitShip($hitShipId, $userMove);
            $status = $isKilledHitShip ? GameResponseStatusEnum::KILLED : GameResponseStatusEnum::HIT;

            $lastAction['status'] = $status;
            $dataToReturn['status'] = $status;
            $dataToReturn['header'] = $this->translator->trans('game.gameActions.headers.requests.' . ($isKilledHitShip ? 'killed' : 'hit'));
            $dataToReturn['message'] = $this->translator->trans('game.gameActions.messages.requests.' . ($isKilledHitShip ? 'killed' : 'hit'));

            if ($isKilledHitShip) {
                array_push($lastAction['killed'], $hitShipId);

                $shipCoordinates = $this->getCoordinatesForShipById($hitShipId, $userMove);
                $dataToReturn['boardFields'] = $shipCoordinates['boardFields'];
                $dataToReturn['aroundFields'] = $shipCoordinates['aroundFields'];
                $dataToReturn['killed'] = $lastAction['killed'];
            }

            $this->addShipToHitInLastAction($lastAction, $hitShipId, $userMove);
        } else {
            array_push($lastAction['mishits'], $data['coordinates']);
            $lastAction['status'] = GameResponseStatusEnum::MISS_HIT;
        }

        if (array_key_exists('ai_move', $data)) {
            $isHuntedMode = $previousAction !== null && in_array($previousAction['status'], [
                    GameResponseStatusEnum::HUNTED,
                    GameResponseStatusEnum::HUNTED_AND_HIT,
                    GameResponseStatusEnum::HIT
                ]);

            if (($lastAction['status'] !== GameResponseStatusEnum::KILLED && $isHuntedMode) || $lastAction['status'] === GameResponseStatusEnum::HIT) {
                $newStatus = $lastAction['status'] === GameResponseStatusEnum::HIT ? GameResponseStatusEnum::HUNTED_AND_HIT : GameResponseStatusEnum::HUNTED;
                $lastAction['status'] = $newStatus;
            }
        }

        $this->saveLastAction($lastAction);

        if ($endGameData = $this->getEndGameData()) {
            return $endGameData;
        }

        if (in_array($lastAction['status'], [GameResponseStatusEnum::MISS_HIT, GameResponseStatusEnum::HUNTED])) {
            $this->changeTurn();
            $dataToReturn['yourTurn'] = $this->isYourTurn();
        }

        return $dataToReturn;
    }

    /**
     * @throws \Exception
     */
    private function isKilledHitShip(int $shipId, bool $userMove): bool
    {
        $ship = $this->findShipByIdInUserShips($shipId, $userMove);
        if ($ship === null) {
            return false;
        }

        if ($ship['elementsCount'] === 1) {
            return true;
        }

        $lastAction = $this->getLastOpponentAction($userMove);
        if ($lastAction === null) {
            return false;
        }

        if (
            array_key_exists($shipId, $lastAction['hit']) &&
            count($lastAction['hit'][$shipId]['hit']) === $ship['elementsCount'] - 1
        ) {
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

    /**
     * @throws \Exception
     */
    private function addShipToHitInLastAction(array &$lastAction, int $hitShipId, bool $userMove)
    {
        $ship = $this->findShipByIdInUserShips($hitShipId, $userMove);

        if (array_key_exists($hitShipId, $lastAction['hit'])) {
            array_push(
                $lastAction['hit'][$hitShipId]['hit'],
                $this->findNumberOfHitElementInShip($lastAction['coordinates'], $ship)
            );
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