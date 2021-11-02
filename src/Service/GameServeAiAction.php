<?php

namespace App\Service;

use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameServeAiAction extends GameServePlayer
{
    private GameServeActionPlayer $serveActionPlayer;
    private Container $container;

    public function __construct(GameServeActionPlayer $serveActionPlayer, EntityManagerInterface $em, Security $security, TranslatorInterface $translator, Container $container)
    {
        parent::__construct($em, $security, $translator);

        $this->serveActionPlayer = $serveActionPlayer;
        $this->container = $container;
    }

    /**
     * @throws \Exception
     */
    public function serveAction(): ?array
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

        $this->serveMove();

        return null;
    }

    /**
     * @throws \Exception
     */
    private function serveMove(): void
    {
        $data = [
            'coordinates' => $this->calculateCoordinatesToShot(),
        ];


        $this->serveActionPlayer->serveAction($data);
    }

    /**
     * @throws \Exception
     */
    private function calculateCoordinatesToShot(): string
    {
        $lastAction = $this->getLastOpponentAction(true);
        if ($lastAction === null) {
            return $this->calculateCoordinatesForFreeShot();
        }

        if ($lastAction['status'] === GameResponseStatusEnum::KILLED) {
            return $this->calculateCoordinatesForFreeShot();
        }

        if ($lastAction['status'] === GameResponseStatusEnum::MISS_HIT) {
            // TODO counter mishits
            return $this->calculateCoordinatesForFreeShot();  // change it!
        }

        $hitShipId = $this->findLastHitShipIdInAction($lastAction);


        return '';
    }

    /**
     * @throws \Exception
     */
    private function calculateCoordinatesForFreeShot(): string
    {
        $unavailableCoordinatesFromOpponent = $this->getUnavailableCoordinates(true);
        do {
            $coordinates = $this->drawCoordinates();
        } while (in_array($coordinates, $unavailableCoordinatesFromOpponent));

        return $coordinates;
    }

    /**
     * @throws \Exception
     */
    private function drawCoordinates(): string
    {
        $boardSize = $this->container->getParameter('board_size');
        $coordinates = '';
        $letter = chr(rand(
            ord('A'),
            ord('A') + $boardSize
        ));
        $number = rand(1, $boardSize);

        return $coordinates;
    }

    /**
     * @throws \Exception
     */
    private function getActions(bool $forOpponent = false): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $dataToReturn = [];
        $gameInfo = $this->getGameInfo();
        $turnFlag = $this->getUserPositionInQueue($forOpponent);

        array_splice($gameInfo, (int)!$turnFlag, 1);
        array_shift($gameInfo);

        foreach ($gameInfo as &$info) {
            if ($info['userAction'] === $user->getId()) {
                $dataToReturn[] = $info;
            }
        }

        return $dataToReturn;
    }

    /**
     * @throws \Exception
     */
    private function getUnavailableCoordinates(bool $forOpponent): array
    {
        $appendCoordinatesToData = function ($action) use (&$dataToReturn) {
            $status = GameResponseStatusEnum::MISS_HIT;

            switch ($action['status']) {
                case GameResponseStatusEnum::HIT:
                    $status = GameResponseStatusEnum::HIT;
                    break;
                case GameResponseStatusEnum::KILLED:
                    $status = GameResponseStatusEnum::KILLED;
                    break;
            }
            array_push($dataToReturn[$status], $action['coordinates']);
        };

        $dataToReturn = [
            GameResponseStatusEnum::MISS_HIT => [],
            GameResponseStatusEnum::HIT => [],
            GameResponseStatusEnum::KILLED => [],
        ];
        $opponentActions = $this->getActions($forOpponent);

        foreach ($opponentActions as $action) {
            $appendCoordinatesToData($action);
        }

        return $dataToReturn;
    }

    private function findLastHitShipIdInAction(array $action): int
    {
        foreach (array_keys($action['hit']) as $shipHitId) {
            if (!in_array($shipHitId, $action['killed'])) {
                return $shipHitId;
            }
        }

        throw new \Exception("In action with position {$action['positionInGameInfo']} any hit ship has been found!");
    }
}