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
                "createdDate" => "2022-10-01 11:47:43"
            ],
            [
                "trick" => "Indy",
                "user" => "thomas",
                "content" => "je m'entraine tous les jours depuis un mois et je ne maîtrise toujours pas.",
                "createdDate" => "2022-10-01 11:47:45"
            ],
            [
                "trick" => "Indy",
                "user" => "mathilde",
                "content" => "pareil pour moi",
                "createdDate" => "2022-10-01 11:47:50"
            ],
            [
                "trick" => "Indy",
                "user" => "ayoub",
                "content" => "il faut mettre son poid vers l'arriere",
                "createdDate" => "2022-10-01 12:47:40"
            ],
            [
                "trick" => "Indy",
                "user" => "david",
                "content" => "je vais poster un tuto bientôt sur youtube",
                "createdDate" => "2022-10-01 13:47:43"
            ],
            [
                "trick" => "Indy",
                "user" => "mathilde",
                "content" => "chouette",
                "createdDate" => "2022-10-01 13:57:45"
            ],
            [
                "trick" => "Indy",
                "user" => "ayoub",
                "content" => "tu pourras la poster sur snowtricks ?",
                "createdDate" => "2022-10-01 14:47:50"
            ],
            [
                "trick" => "Indy",
                "user" => "thomas",
                "content" => "+1",
                "createdDate" => "2022-10-01 15:47:40"
            ],
            [
                "trick" => "Indy",
                "user" => "david",
                "content" => "oui biensur je le posterai directement",
                "createdDate" => "2022-10-01 16:47:43"
            ],
            [
                "trick" => "Indy",
                "user" => "thomas",
                "content" => "je me suis aboner à ta chaine",
                "createdDate" => "2022-10-01 17:47:45"
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
