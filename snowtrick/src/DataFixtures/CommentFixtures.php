<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\TrickFixtures;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{

    private function getComments()
    {
        return [
            [
                "trick" => "Indy",
                "user" => "thomas",
                "content" => "pas simple a réaliser",
                "createdDate" => "2022-10-01 11:47:40"
            ],
            [
                "trick" => "Indy",
                "user" => "david",
                "content" => "il faut juste un peu entrainment",
                "createdDate" => "2022-10-01 11:47:40"
            ],
            [
                "trick" => "Indy",
                "user" => "thomas",
                "content" => "je m'entraine tous les jours depuis un mois et je ne maîtrise toujours pas.",
                "createdDate" => "2022-10-01 11:47:40"
            ],
            [
                "trick" => "Indy",
                "user" => "mathilde",
                "content" => "pareil pour moi",
                "createdDate" => "2022-10-01 11:47:40"
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $comments = $this->getComments();
        foreach ($comments as $comment) {
            $user = $this->getReference(UserFixtures::USER_REF . $comment['user']);
            $trick = $this->getReference(TrickFixtures::TRICK_REF . $comment['trick']);

            $newComment = new Comment();
            $newComment->setContent($comment['content']);
            $newComment->setUser($user);
            $newComment->setTrick($trick);
            $newComment->setCreatedDate(new \Datetime($comment['createdDate']));

            $manager->persist($newComment);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class
        ];
    }
}
