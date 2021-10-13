<?php

namespace App\Entity\Enums;

class GameResponseStatusEnum
{
    const NO_CHANGED = 'no_changed';
    const CHANGE_TURN = 'change_turn';
    const SHOT = 'shot';
    const END_GAME = 'end_game';
    const WALKOVER = 'walkover';

    static public function isValid(string $status): bool
    {
        return in_array($status, self::serialise());
    }

    static public function serialise(): array
    {
        return [
            'no_changed' => self::NO_CHANGED,
            'change_turn' => self::CHANGE_TURN,
            'shot' => self::SHOT,
            'end_game' => self::END_GAME,
            'walkover' => self::WALKOVER,
        ];
    }
}