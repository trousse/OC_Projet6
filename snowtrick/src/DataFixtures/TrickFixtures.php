<?php

namespace App\DataFixtures;

use App\DataFixtures\ImageFixtures;
use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{

    public const TRICK_REF = "trick_";

    private function getTricks()
    {
        return [
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Indy",
                "description" => "saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.",
                "groupe" => "Grab",
                "mainImage" => "Indy.jpeg",
                "images" => ["Indy2.jpeg"],
                "videos" => ["Indy"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Stalefish",
                "description" => "saisie de la carre backside de la planche entre les deux pieds avec la main arrière",
                "groupe" => "Grab",
                "mainImage" => "stalfish.jpeg",
                "images" => ["stalfish2.jpeg","stalfish3.jpeg"],
                "videos" => ["Stalefish"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Truck driver",
                "description" => "saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)",
                "groupe" => "Grab",
                "mainImage" => "truck_driver.jpeg",
                "images" => [],
                "videos" => []
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Big foot",
                "description" => "1080 ou big foot pour trois tours",
                "groupe" => "Rotation",
                "mainImage" => "big_foot.jpeg",
                "images" => [],
                "videos" => []
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Front flips",
                "description" => "une rotation verticale vers l'avant",
                "groupe" => "Flip",
                "mainImage" => "front_flip.jpeg",
                "images" => [ "front_flip2.jpeg"],
                "videos" => ["Frontflip"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Back flips",
                "description" => "une rotation verticale vers arriere",
                "groupe" => "Flip",
                "mainImage" => "back_flip.jpeg",
                "images" => [ "back_flip2.jpeg"],
                "videos" => ["Backflip"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Nose slide",
                "description" => "Glisser sur une barre de slide avec l'avant de la planche",
                "groupe" => "Slide",
                "mainImage" => "nose_slide.jpeg",
                "images" => [ "nose_slide2.jpeg","nose_slide3.jpeg" ,"nose_slide4.jpeg"],
                "videos" => ["Noseslide"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Tall slide",
                "description" => "Glisser sur une barre de slide avec l'arriere de la planche",
                "groupe" => "Slide",
                "mainImage" => "tall_slide.jpeg",
                "images" => [ "tall_slide2.jpeg"],
                "videos" => []
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Japan air",
                "description" => "La main avant s'enroule autour de la jambe avant et attrape le bord des orteils entre les fixations. Les genoux sont pliés en repliant les jambes vers l'arrière vers la planche",
                "groupe" => "Old School",
                "mainImage" => "Japan.jpeg",
                "images" => [ "Japan2.jpeg"],
                "videos" => ["Japan"]
            ],
            [
                "creatingDate" => "2022-10-01 11:47:40",
                "modifingDate" => "2022-10-01 11:47:40",
                "name" => "Rocket air",
                "description" => "Les deux mains saisissent le nez de votre planche en même temps",
                "groupe" => "Old School",
                "mainImage" => "rocket.jpeg",
                "images" => [ "rocket2.jpeg"],
                "videos" => ["Rocket"]
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $tricks = $this->getTricks();
        foreach ($tricks as $trick){
            $newtrick = new trick();
            $groupe =  $this->getReference(TrickGroupeFixtures::GROUPE_REF.$trick['groupe']);

            $newtrick->setDescription($trick['description']);
            $newtrick->setCreatingDate( new \Datetime($trick['creatingDate']));
            $newtrick->setName($trick['name']);
            $newtrick->setModifingDate(new \Datetime($trick['modifingDate']));
            $newtrick->setMainImage($trick['mainImage']);
            $newtrick->setgroupe($groupe);

            $imageDirectory = __DIR__.'/../../public/images/photos/trick_'.$newtrick->getSlug();
            if (!file_exists($imageDirectory)) mkdir($imageDirectory, 0777, true);
            copy(__DIR__.'/images/'.$trick['mainImage'],$imageDirectory.'/'.$trick['mainImage']);

            foreach ($trick['images'] as $image){
                $imageRef = $this->getReference(ImageFixtures::IMAGE_REF.$image);
                $newtrick->addImage($imageRef);
                if (!file_exists($imageDirectory)) mkdir($imageDirectory, 0777, true);
                copy(__DIR__.'/images/'.$image,$imageDirectory.'/'.$image);
            }

            foreach ($trick['videos'] as $video) {
                $videoRef = $this->getReference(VideoFixtures::VIDEO_REF . $video);
                $newtrick->addVideo($videoRef);
            }

            $this->addReference(self::TRICK_REF. $newtrick->getName(), $newtrick);

            $manager->persist($newtrick);


        }


        $manager->flush();

    }


    public function getDependencies()
    {
        return [
            ImageFixtures::class,
            VideoFixtures::class,
            TrickGroupeFixtures::class
        ];
    }
}
