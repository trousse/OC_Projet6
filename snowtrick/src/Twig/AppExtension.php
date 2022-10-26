<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use YoHang88\LetterAvatar\LetterAvatar;


class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('avatar', [$this, 'avatar']),
        ];
    }

    public function avatar(string $name, string $shape = 'circle', int $size = 32): string
    {
        $avatar = new LetterAvatar($name, $shape, $size);

        return $avatar;
    }
}