<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Picture;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $date = new DateTime('now');

        for ($i = 1; $i <= 7; $i++) {

            $manager->persist(
                (new Category())
                    ->setName('Categorie n°' . $i)
                    ->setSlug('categorie-' . $i)
            );
        }

        //Did not succed in matching here the tricks and categories so I did it in the database directly

        for ($j = 1; $j <= 14; $j++) {

            $manager->persist(
                (new Trick())
                    ->setName('Trick n°' . $j)
                    ->setSlug('trick-' . $j)
                    ->setDescription("Courte description de comment réaliser le trick, histoire d'avoir quelque chose dans la description.")
                    ->setCreationDate($date)
                    ->addPicture(
                        (new Picture())
                            ->setUrl("/public/assets/img/pictures/picture_1")
                    )
                    ->addPicture(
                        (new Picture())
                            ->setUrl("/public/assets/img/pictures/picture_2")
                    )
                    ->addVideo(
                        (new Video())
                            ->setUrl("https://youtu.be/AzJPhQdTRQQ")
                    )
                    ->addVideo(
                        (new Video())
                            ->setUrl("https://youtu.be/SQNc3VBOgEM")
                    )
            );
        }

        $manager->flush();
    }
}
