<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Picture;
use App\Entity\User;
use DateTime;
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
        for ($u = 1; $u <= 6; $u++) {
            $user = new User;
            $hash = $this->hasher->hashPassword($user, "password$u");
            $manager->persist(
                $user
                    ->setEmail("user$u@gmail.com")
                    ->setPassword($hash)
                    ->setIdPhotoPath("/uploads/idPhotos/user$u.png")
                    ->setScreenName("user$u")
            );
        }

        $index = 1; 

        for ($i = 1; $i <= 7; $i++) {
            $category = new Category;
            $manager->persist(
                $category
                    ->setName('Categorie ' . $i)
                    ->setSlug('categorie-' . $i)
            );

            for ($j = 1; $j <= 3; $j++) {
                $manager->persist(
                    (new Trick())
                        ->setName('Trick ' . $index)
                        ->setSlug('trick-' . $index)
                        ->setDescription("Courte description de comment réaliser le trick, histoire d'avoir quelque chose dans la description. Et aussi parce que les lorem ipsum, ça suffit !")
                        ->setCreationDate(new DateTime('now'))
                        ->setCategory($category)
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
                                ->setUrl("https://youtu.be/AzJPhQdTRQQ")
                        )
                        ->addVideo(
                            (new Video())
                                ->setUrl("https://youtu.be/SQNc3VBOgEM")
                        )
                );
                $index++;
            }
        }
        $manager->flush();
    }
}
