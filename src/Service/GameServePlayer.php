<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameServePlayer
{
    protected EntityManagerInterface $em;
    protected Security $security;
    protected TranslatorInterface $translator;
    protected ParameterBagInterface $parameterBag;

    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator, ParameterBagInterface $parameterBag)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
        $this->parameterBag = $parameterBag;

        $lastActionData = [
            'userAction' => 'user',
            'status' => 'shoot/hit/shoot_down',
            'coordinates' => 'A1',
            'hit' => [
                4 => [
                    'elementsCount' => 3,
                    'hit' => [1, 2],
                ],
                2 => [
                    'elementsCount' => 2,
                    'hit' => [2],
                ],
            ],
            'killed' => [9, 4],
            'positionInGameInfo' => 5,
            'isReading' => 0,
            'mishits' => ['A2', 'F5', 'C6'],
        ];
    }

    /**
     * @throws \Exception
     */
    public function getUserPositionInQueue(bool $searchOpponent = false): int
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $usersIdsInGame = array_map(function ($val) {
            return $val['userId'];
        }, array_slice($game->getGameInfo(), 0, 2));
        $userPositionInQueue = array_search($user->getId(), $usersIdsInGame);

        if ($userPositionInQueue === false) {
            throw new \Exception("User with ID {$user->getId()} has not found in game users!");
        }

        return $searchOpponent ? (int)!$userPositionInQueue : $userPositionInQueue;
    }

    public function getGameInfo(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        return $game->getGameInfo();
    }

    /**
     * @throws \Exception
     */
    public function getOpponent(): User
    {
        $userPosition = $this->getUserPositionInQueue(true);
        $userId = $this->getGameInfo()[$userPosition]['userId'];

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($userId);

        if ($user === null) {
            $user = new User();
            $user->setId($this->parameterBag->get('ai_id'));
        }

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function getLastOpponentAction(bool $findForUser = false): ?array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();
        $gameInfo = $game->getGameInfo();

        if (count($gameInfo) === count($game->getUsers())) {
            return null;
        }

        $actions = [];
        $searchedOpponent = $findForUser ? $user : $this->getOpponent();
        foreach ($gameInfo as $index => $action) {
            if (in_array($index, range(0, count($game->getUsers()) - 1)) || $action['userAction'] !== $searchedOpponent->getId()) {
                continue;
            }

            $actions[] = $action;
        }

        if (empty($actions)) {
            return null;
        }

        return $actions[count($actions) - 1];
    }

    /**
     * @throws \Exception
     */
    public function isGameOver(): bool
    {
        $lastAction = $this->getLastOpponentAction(true);
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo());
    }

    /**
     * @throws \Exception
     */
    public function isVictory(): bool
    {
        $lastAction = $this->getLastOpponentAction();
        if (!$lastAction) {
            return false;
        }

        return count($lastAction['killed']) === count($this->getUserShipsInfo(true));
    }

    /**
     * @throws \Exception
     */
    public function getUserShipsInfo(bool $getOpponentInfo = false): array
    {
        $userPositionInQueue = $this->getUserPositionInQueue($getOpponentInfo);
        return $this->getGameInfo()[$userPositionInQueue]['ships'];
    }

    /**
     * @throws \Exception
     */
    public function getEndGameData(): ?array
    {
        if ($this->isGameOver()) {
            // game over
            return [];
        }

        if ($this->isVictory()) {
            // victory
            return [];
        }

        return null;
    }

    public function generateEmptyLastAction(bool $forUser = true): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        return [
            'userAction' => $forUser ? $user->getId() : $this->parameterBag->get('ai_id'),
            'status' => null,
            'coordinates' => null,
            'hit' => [],
            'killed' => [],
            'positionInGameInfo' => count($game->getGameInfo()),
            'isReading' => false,
            'mishits' => [],
        ];
    }

    public function markActionAsRead($action): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $action['isReading'] = true;
        $gameInfo = $game->getGameInfo();
        $gameInfo[$action['positionInGameInfo']] = $action;
        $game->setGameInfo($gameInfo);

        $this->em->persist($game);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function findShipByIdInUserShips(int $shipId, bool $forOpponent = false): ?array
    {
        $userShipsInfo = $this->getUserShipsInfo($forOpponent);

        foreach ($userShipsInfo as $ship) {
            if ($ship['id'] === $shipId) {
                return $ship;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function isYourTurn(): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $game = $user->getGame();

        $turnFlag = $this->getUserPositionInQueue();

        return $game->getPlayerTurn() === $turnFlag;
    }

    /**
     * @throws \Exception
     */
    public function getCoordinatesForShipById(int $shipId, bool $forOpponent = false): array
    {
        $dataToReturn = [
            'boardFields' => [],
            'aroundFields' => [],
        ];

        $ship = $this->findShipByIdInUserShips($shipId, $forOpponent);
        if ($ship === null) {
            throw new \Exception("Ship with ID $shipId has not been found for " . ($forOpponent ? 'opponent' : 'user'));
        }

        foreach ($ship['boardFields'] as $boardField) {
            array_push($dataToReturn['boardFields'], $boardField['coordinates']);
        }
        foreach ($ship['aroundFields'] as $aroundField) {
            array_push($dataToReturn['aroundFields'], $aroundField['coordinates']);
        }

        return $dataToReturn;
    }

    public function findShipIdByCoordinates(array $ships, string $coordinates): ?int
    {
        foreach ($ships as $ship) {
            foreach ($ship['boardFields'] as $boardField) {
                if ($boardField['coordinates'] === $coordinates) {
                    return $ship['id'];
                }
            }
        }

        return null;
    }
}