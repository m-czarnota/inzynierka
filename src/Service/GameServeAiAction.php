<?php

namespace App\Service;

use App\Entity\Enums\GameRequestStatusEnum;
use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;

class GameServeAiAction
{
    private GameServeActionPlayer $serveActionPlayer;

    public function __construct(GameServeActionPlayer $serveActionPlayer)
    {
        $this->serveActionPlayer = $serveActionPlayer;
    }

    /**
     * @throws \Exception
     */
    public function serveAction(): void
    {
        /** @var User $user */
        $user = $this->serveActionPlayer->getSecurity()->getUser();
        $game = $user->getGame();

        if (!$game) {
            throw new \Exception($this->serveActionPlayer->getTranslator()->trans('game.gameActions.requests.notInGame'));
        }

        $this->serveMove();
    }

    /**
     * @throws \Exception
     */
    private function serveMove(): void
    {
        $data = [
            'action' => GameRequestStatusEnum::SHOT,
            'coordinates' => $this->calculateCoordinatesToShot(),
            'ai_move' => true,
        ];


        $this->serveActionPlayer->serveAction($data);
    }

    /**
     * @throws \Exception
     */
    private function calculateCoordinatesToShot(): string
    {
        $lastAction = $this->serveActionPlayer->getLastOpponentAction();
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

        $actions = $this->getActualHuntedActions();
        $coordinates = '';

        foreach (array_reverse($actions) as $action) {
            $coordinates = $this->calculateCoordinatesForHuntedShot($action['coordinates']);
            if ($coordinates !== null) {
                break;
            }
        }

        return $coordinates;
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
    private function calculateCoordinatesForHuntedShot(string $coordinates): ?string
    {
        $letter = substr($coordinates, 0, 1);
        $number = (int)substr($coordinates, 1);
        $boardSize = $this->serveActionPlayer->getParameterBag()->get('board_size');

        $letterMoves = [
            'up' => -1,
            'down' => 1,
            'none' => 0
        ];
        $numberMoves = [
            'left' => -1,
            'right' => 1,
            'none' => 0
        ];

        $unavailableCoordinates = $this->getUnavailableCoordinates(true);

        // TODO more random!
        foreach ($letterMoves as $letterMove) {
            foreach ($numberMoves as $numberMove) {
                $newLetter = chr(ord($letter) + $letterMove);
                $newNumber = $newLetter === $letter ? ($number + $numberMove) : $number;

                $isLetterInBoardBorder = $newLetter >= 'A' && $newLetter < chr(ord('A') + $boardSize);
                $isNumberInBoardBorder = $newNumber >= 1 && $newNumber <= $boardSize;
                if (!$isLetterInBoardBorder || !$isNumberInBoardBorder) {
                    continue;
                }

                $newCoordinates = $newLetter . $newNumber;
                if (in_array($newCoordinates, $unavailableCoordinates)) {
                    continue;
                }

                return $newCoordinates;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    private function drawCoordinates(): string
    {
        $boardSize = $this->serveActionPlayer->getParameterBag()->get('board_size');
        $letter = chr(rand(
            ord('A'),
            ord('A') + $boardSize - 1
        ));
        $number = rand(1, $boardSize);

        return $letter . $number;
    }

    /**
     * @throws \Exception
     */
    private function getActions(bool $forOpponent = false): array
    {
        $aiId = $this->serveActionPlayer->getParameterBag()->get('ai_id');

        $dataToReturn = [];
        $gameInfo = $this->serveActionPlayer->getGameInfo();
        $turnFlag = $this->serveActionPlayer->getUserPositionInQueue($forOpponent);

        array_splice($gameInfo, (int)!$turnFlag, 1);
        array_shift($gameInfo);

        foreach ($gameInfo as &$info) {
            if ($info['userAction'] === $aiId) {
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
        $opponentActions = $this->getActions($forOpponent);
        $dataToReturn = array_map(function ($action) {
            return $action['coordinates'];
        }, $opponentActions);

        return $dataToReturn;
    }

    /**
     * @throws \Exception
     */
    private function getSortedUnavailableCoordinates(bool $forOpponent): array
    {
        $appendCoordinatesToData = function ($action) use (&$dataToReturn) {
            $status = GameResponseStatusEnum::MISS_HIT;

            switch ($action['status']) {
                case GameResponseStatusEnum::HIT:
                    $status = GameResponseStatusEnum::HIT;
                    break;
                case GameResponseStatusEnum::HUNTED_AND_HIT:
                    $status = GameResponseStatusEnum::HUNTED_AND_HIT;
                    break;
                case GameResponseStatusEnum::HUNTED:
                    $status = GameResponseStatusEnum::HUNTED;
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
            GameResponseStatusEnum::HUNTED_AND_HIT => [],
            GameResponseStatusEnum::HUNTED => [],
        ];
        $opponentActions = $this->getActions($forOpponent);

        foreach ($opponentActions as $action) {
            $appendCoordinatesToData($action);
        }

        return $dataToReturn;
    }

    /**
     * @throws \Exception
     */
    private function getActualHuntedActions(): array
    {
        $actions = $this->getActions(true);
        $huntedActions = [];

        foreach (array_reverse($actions) as $action) {
            if ($action['status'] === GameResponseStatusEnum::HUNTED_AND_HIT) {
                $huntedActions[] = $action;
            } else if (in_array($action['status'], [GameResponseStatusEnum::KILLED, GameResponseStatusEnum::MISS_HIT])) {
                break;
            }
        }

        return $huntedActions;
    }
}