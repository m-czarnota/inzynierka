<?php

namespace App\Service;

use App\Entity\Enums\KindOfGameEnum;
use App\Entity\Game;
use App\Entity\GameRoom;
use App\Entity\MatchmakingStorage;
use App\Entity\User;
use App\Repository\MatchmakingStorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MatchmakingEngine
{
    private MatchmakingStorageRepository $matchmakingStorageRepository;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private ParameterBagInterface $parameterBag;

    public function __construct(LoggerInterface $logger, MatchmakingStorageRepository $matchmakingStorageRepository, EntityManagerInterface $em, ParameterBagInterface $parameterBag)
    {
        $this->logger = $logger;
        $this->matchmakingStorageRepository = $matchmakingStorageRepository;
        $this->em = $em;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param User $user
     * @param array $userGameInfo
     * @param int $whichApproach
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function searchOpponent(User $user, array $userGameInfo, int $whichApproach): ?User
    {
        $this->logger->info("Search opponent for {$user->getEmail()} with id {$user->getId()}");

        $matchmakingPosition = $this->matchmakingStorageRepository->findOneMatchmakingByUser($user);
        if (!$matchmakingPosition) {
            $matchmakingPosition = $this->createMatchmakingPosition($user, $userGameInfo);
        }

        if ($whichApproach === 1) {
            $ships = ['userShips' => json_decode($userGameInfo['ships'])];
            if (array_key_exists('aiShips', $userGameInfo)) {
                $ships['aiShips'] = json_decode($userGameInfo['aiShips']);
            }

            $matchmakingPosition->setShips($ships);
            $matchmakingPosition->setKindOfGame($userGameInfo['kindOfGame']);
            $matchmakingPosition->setCreatedAt(new \DateTime());
            $matchmakingPosition->setUpdatedAt(new \DateTime());

            $this->em->persist($matchmakingPosition);
            $this->em->flush();
        }

        if (in_array($userGameInfo['kindOfGame'], [KindOfGameEnum::GAME_AI, KindOfGameEnum::GAME_AI_RANKED])) {
            $this->logger->info("Found user AI");
            return $user;
        }

        $usersInMatchmaking = [];
        /** @var MatchmakingStorage $activeMatchmaking */
        foreach ($this->matchmakingStorageRepository->findActiveMatchmakingForUserByKindOfGame($user, $userGameInfo['kindOfGame']) as $activeMatchmaking) {
            $usersInMatchmaking[] = $activeMatchmaking->getUser();
        }

        if (empty($usersInMatchmaking)) {
            $this->logger->info("Opponent for {$user->getEmail()} with id {$user->getId()} has not been found");
            return null;
        }

        $opponent = $this->selectOpponent($user, $usersInMatchmaking);

        $this->logger->info("Found user {$opponent->getEmail()}");

        return $opponent;
    }

    /**
     * @param User $user
     * @param User $opponent
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveFoundUsers(User $user, User $opponent): array
    {
        $this->logger->info("Connecting {$user->getEmail()} ({$user->getId()}) with {$opponent->getEmail()} ({$opponent->getId()})");

        if ($user->getGame()) {
            return [
                'linkToRoom' => $user->getGameRoom()->getLink(),
            ];
        }

        $gameRoom = new GameRoom();
        $game = new Game();

        $gameRoom->setLink(bin2hex(random_bytes(5)));
        $gameRoom->setIsActive(true);

        $userInMatchmaking = $this->matchmakingStorageRepository->findOneMatchmakingByUser($user);
        $game->setKindOfGame($userInMatchmaking->getKindOfGame());

        $players = [$user, $opponent];

        $gameRoom->setGame($game);
        $game->setGameRoom($gameRoom);
        $game->setPlayerTurn(rand(0, 1));

        foreach ($players as &$player) {
            $player->setGameRoom($gameRoom);
            $player->setGame($game);

            $this->em->persist($player);
        }
        unset($player);

        $this->em->persist($gameRoom);
        $this->em->persist($game);

        $gameInfo = [];
        if (in_array($game->getKindOfGame(), [KindOfGameEnum::GAME_AI, KindOfGameEnum::GAME_AI_RANKED])) {
            $ships = $this->matchmakingStorageRepository->findOneMatchmakingByUser($user)->getShips();
            $gameInfo[] = [
                'userId' => $user->getId(),
                'ships' => $ships['userShips']
            ];
            $gameInfo[] = [
                'userId' => $this->parameterBag->get('ai_id'),
                'ships' => $ships['aiShips']
            ];
        } else {
            foreach ($players as &$player) {
                $ships = $this->matchmakingStorageRepository->findOneMatchmakingByUser($player)->getShips();
                $ships = [
                    'userId' => $player->getId(),
                    'ships' => $ships['userShips'],
                ];
                $gameInfo[] = $ships;
            }
            unset($player);
        }
        $game->setGameInfo($gameInfo);

        $this->em->flush();

        foreach ($players as &$player) {
            $this->removeUserFromMatchmaking($player);
        }
        unset($player);

        return [
            'linkToRoom' => $gameRoom->getLink(),
        ];
    }

    /**
     * @param User $user
     * @param array $userGameInfo
     * @return MatchmakingStorage
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createMatchmakingPosition(User $user, array $userGameInfo): MatchmakingStorage
    {
        $ships = ['userShips' => json_decode($userGameInfo['ships'])];
        if (array_key_exists('aiShips', $userGameInfo)) {
            $ships['aiShips'] = json_decode($userGameInfo['aiShips']);
        }

        $matchmakingStorage = new MatchmakingStorage();
        $matchmakingStorage->setUser($user);
        $matchmakingStorage->setShips($ships);
        $matchmakingStorage->setKindOfGame($userGameInfo['kindOfGame']);

        $this->em->persist($matchmakingStorage);
        $this->em->flush();

        return $matchmakingStorage;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function removeUserFromMatchmaking(User $user): void
    {
        $this->logger->info('Remove user from matchmaking');

        $matchmakingUserPosition = $this->matchmakingStorageRepository->find($user->getId());
        if ($matchmakingUserPosition) {
            $this->logger->info("Remove user {$user->getEmail()} with id {$user->getId()}");

            $this->em->remove($matchmakingUserPosition);
            $this->em->flush();
        }
    }

    /**
     * @param User $user
     * @param array $opponents
     * @return User
     */
    private function selectOpponent(User $user, array $opponents): User
    {
        return $opponents[0];
    }
}