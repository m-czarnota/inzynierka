<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Base64Encode extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('base64_encode', [$this, 'base64encode']),
            new TwigFilter('base64_decode', [$this, 'base64decode']),
        ];
    }

    public function base64encode(string $string): string
    {
        return base64_encode($string);
    }

    public function base64decode(string $string): string
    {
        return base64_decode($string);
    }
}