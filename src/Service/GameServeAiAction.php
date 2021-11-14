<?php

namespace App\Service;

use App\Entity\Enums\GameRequestStatusEnum;
use App\Entity\Enums\GameResponseStatusEnum;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameServeAiAction extends GameServePlayer
{
    private GameServeActionPlayer $serveActionPlayer;

    public function __construct(GameServeActionPlayer $serveActionPlayer, EntityManagerInterface $em, Security $security, TranslatorInterface $translator)
    {
        parent::__construct($em, $security, $translator);

        $this->serveActionPlayer = $serveActionPlayer;
    }

    /**
     * @throws \Exception
     */
    public function serveAction(): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        if (!$game) {
            throw new \Exception($this->translator->trans('game.gameActions.requests.notInGame'));
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

        return $this->calculateCoordinatesForHuntedShot($lastAction['coordinates']);
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
    private function calculateCoordinatesForHuntedShot(string $coordinates): string
    {
        $letter = substr($coordinates, 0, 1);
        $number = (int)substr($coordinates, 1);
        $boardSize = $this->parameterBag->get('board_size');

        $letterMoves = [
            'up' => -$boardSize,
            'down' => $boardSize,
            'none' => 0
        ];
        $numberMoves = [
            'left' => -1,
            'right' => 1,
            'none' => 0
        ];

        $unavailableCoordinates = $this->getUnavailableCoordinates(true);

        do {
            $newLetter = chr(ord($letter) - $letterMoves[array_keys($letterMoves)[rand(0, count($letterMoves) - 1)]]);
            $newNumber = $number - $numberMoves[array_keys($numberMoves)[rand(0, count($numberMoves) - 1)]];

            $isLetterInBoardBorder = $newLetter >= ord('A') && $newLetter <= (ord('A') + $boardSize);
            $isNumberInBoardBorder = $newNumber >= 1 && $newNumber <= $boardSize;

            $newCoordinates = $newLetter . $newNumber;
        } while (!$isLetterInBoardBorder || !$isNumberInBoardBorder || in_array($newNumber, $unavailableCoordinates));

        return $newCoordinates;
    }

    /**
     * @throws \Exception
     */
    private function drawCoordinates(): string
    {
        $boardSize = $this->parameterBag->get('board_size');
        $letter = chr(rand(
            ord('A'),
            ord('A') + $boardSize
        ));
        $number = rand(1, $boardSize);

        return $letter . $number;
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