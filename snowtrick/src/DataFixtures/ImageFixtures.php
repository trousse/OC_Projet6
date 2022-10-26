<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{

    public const IMAGE_REF = 'image_';

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $imagesNames = scandir(__DIR__.'/images');
        foreach ($imagesNames as $imageName){
            $newImage = new Image();

            $newImage->setName($imageName);
            $this->addReference(self::IMAGE_REF. $imageName, $newImage);
            $manager->persist($newImage);

        }

        $manager->flush();
    }
}
