<?php

namespace App\DataFixtures;

use App\Entity\TrickGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickGroupeFixtures extends Fixture
{
    public const GROUPE_REF = "groupe_";

    private function getGroupes(){
        return [
            "Grab",
            "Rotation",
            "Flip",
            "Slide",
            "Old School"
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $groupes = $this->getGroupes();
        foreach ($groupes as $groupe){
            $newGroupe = new TrickGroup();
            $newGroupe->setName($groupe);

            $this->addReference(self::GROUPE_REF. $groupe, $newGroupe);
        }

        $manager->flush();
    }
}
