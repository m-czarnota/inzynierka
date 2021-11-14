<?php

namespace App\Entity\Enums;

class KindOfGameEnum
{
    const GAME_AI = 0;
    const GAME_AI_RANKED = 1;
    const GAME_FRIEND = 2;
    const GAME_FRIEND_RANKED = 3;
    const GAME_RANDOM = 4;
    const GAME_RANDOM_RANKED = 5;

    public static function isValid($kindOfGame): bool
    {
        $kindOfGame = filter_var($kindOfGame, FILTER_VALIDATE_INT);

        return in_array($kindOfGame, self::serialize());
    }

    public static function serialize(): array
    {
        return [
            'game_ai' => self::GAME_AI,
            'game_ai_ranked' => self::GAME_AI_RANKED,
            'game_friend' => self::GAME_FRIEND,
            'game_friend_ranked' => self::GAME_FRIEND_RANKED,
            'game_random' => self::GAME_RANDOM,
            'game_random_ranked' => self::GAME_RANDOM_RANKED,
        ];
    }
}