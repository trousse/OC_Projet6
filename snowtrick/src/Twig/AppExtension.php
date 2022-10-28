<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use YoHang88\LetterAvatar\LetterAvatar;


class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('avatar', [$this, 'avatar']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('pagination', [$this, 'pagination']),
        ];
    }

    public function pagination($page, $nbResult, $nbByPage = 10, $nbAround = 3)
    {

        $nbTotalPage = (int)($nbResult / $nbByPage) + (($nbResult % $nbByPage) > 0 ? 1 : 0) - 1 ;
        $nbPageAfterEnd = $nbTotalPage - $page;
        $nbPaginationNumber = ($nbAround * 2) + 1;

        if ($nbTotalPage < $nbPaginationNumber - 1) {
            return [
                "start" => 0,
                "end" => $nbTotalPage
            ];
        }

        $remove = $nbAround + ($nbPageAfterEnd > $nbAround ? 0 : $nbAround - $nbPageAfterEnd);
        $add = $page <= $nbAround ? $nbAround - $page : -1;

        return [
            "start" => $page + $add - $remove,
            "end" => $page + $nbPaginationNumber + $add - $remove
        ];
    }

    public function avatar(string $name, string $shape = 'circle', int $size = 32): string
    {
        $avatar = new LetterAvatar($name, $shape, $size);

        return $avatar;
    }
}