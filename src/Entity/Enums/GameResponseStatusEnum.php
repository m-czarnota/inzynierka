<?php

namespace App\Entity\Enums;

class GameResponseStatusEnum
{
    const ERROR = 'error';
    const NO_CHANGED = 'no_changed';

    const HIT = 'hit';
    const MISS_HIT = 'mishit';
    const KILLED = 'killed';
    const HUNTED = 'hunted';
    const HUNTED_AND_HIT = 'hunted_and_hit';

    const MISSED_TURN = 'missed_turn';

    const END_GAME = 'end_game';
    const WALKOVER = 'walkover';

    static public function isValid(string $status): bool
    {
        return in_array($status, self::serialise());
    }

    static public function serialise(): array
    {
        return [
            'error' => self::ERROR,
            'no_changed' => self::NO_CHANGED,
            'missed_turn' => self::MISSED_TURN,
            'miss_hit' => self::MISS_HIT,
            'killed' => self::KILLED,
            'hit' => self::HIT,
            'hunted' => self::HUNTED,
            'hunted_and_hit' => self::HUNTED_AND_HIT,
            'end_game' => self::END_GAME,
            'walkover' => self::WALKOVER,
        ];
    }
}