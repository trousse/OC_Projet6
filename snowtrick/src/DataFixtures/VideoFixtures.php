<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public const VIDEO_REF = "video_";

    private function getVideos()
    {
        return [
            [
                "id" => 1,
                "url" => "rtK8qkYwz1U",
                "name" => "Indy",
            ],
            [
                "id"=> 2,
                "url" => "f9FjhCt_w2U",
                "name" => "Stalefish"
            ],
            [
                "id" => 3,
                "url" => "eGJ8keB1-JM",
                "name" => "Frontflip"
            ],
            [
                "id" => 4,
                "url" => "5bpzng08nzk",
                "name" => "Backflip"
            ],
            [
                "id" => 5,
                "url" => "KqSi94FT7EE",
                "name" => "Noseslide"
            ],
            [
                "id" => 6,
                "url" => "jH76540wSqU",
                "name" => "Japan"
            ],
            [
                "id" => 7,
                "url" => "nom7QBoGh5w",
                "name" => "Rocket"
            ]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $videos = $this->getVideos();
        foreach ($videos as $video) {
            $newVideo = new Video();
            $newVideo->setUrl($video['url']);
            $this->addReference(self::VIDEO_REF . $video['name'], $newVideo);
        }

        $manager->flush();
    }
}
