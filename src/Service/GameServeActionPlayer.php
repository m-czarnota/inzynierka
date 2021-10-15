<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GameServeActionPlayer
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;

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
}