<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $trickIndex = 1;

        for ($i = 1; $i <= 7; $i++) {

            $user = new User;
            $hash = $this->hasher->hashPassword($user, "password$i");
            $manager->persist(
                $user
                    ->setEmail("user$i@gmail.com")
                    ->setPassword($hash)
                    ->setIdPhotoPath("/uploads/idPhotos/user$i.png")
                    ->setScreenName("user$i")
            );

            $category = new Category;
            $manager->persist(
                $category
                    ->setName('Categorie ' . $i)
                    ->setSlug('categorie-' . $i)
            );


            for ($j = 1; $j <= 3; $j++) {

                $trick = new Trick;
                $manager->persist(
                    $trick
                        ->setName('Trick ' . $trickIndex)
                        ->setSlug('trick-' . $trickIndex)
                        ->setDescription("Courte description de comment réaliser ce 'fake' trick, histoire d'avoir quelque chose dans la description. Et aussi parce que les lorem ipsum, ça suffit !")
                        ->setCategory($category)
                        ->setUser($user)
                        ->addPicture(
                            (new Picture())
                                ->setPath("/uploads/pictures/picture_1.jpg")
                        )
                        ->addPicture(
                            (new Picture())
                                ->setPath("/uploads/pictures/picture_2.jpg")
                        )
                        ->addPicture(
                            (new Picture())
                                ->setPath("/uploads/pictures/picture_3.jpg")
                        )
                        ->addVideo(
                            (new Video())
                                ->setUrl("https://www.youtube.com/embed/AzJPhQdTRQQ")
                        )
                        ->addVideo(
                            (new Video())
                                ->setUrl("https://www.youtube.com/embed/SQNc3VBOgEM")
                        )
                    );

                $trickIndex++;

                for ($c = 1; $c <= 6; $c++) {
                    $manager->persist(
                        (new Comment())
                            ->setContent("Petit commentaire inutile sur un trick n'existant pas plus que l'utilisateur qui rédige ces mots.")
                            ->setTrick($trick)
                            ->setUser($user)
                    );
                }
            }
        }
        $manager->flush();
    }
}
