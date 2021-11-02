<?php

namespace App\Entity\Enums;

class GameRequestStatusEnum
{
    const MISSED_TURN = 'missed_turn';
    const SHOT = 'shot';

    static public function isValid(string $status): bool
    {
        return in_array($status, self::serialise());
    }

    static public function serialise(): array
    {
        return [
            'missed_turn' => self::MISSED_TURN,
            'shot' => self::SHOT,
        ];
    }
}