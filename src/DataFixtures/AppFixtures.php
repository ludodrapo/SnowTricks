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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $hasher;
    protected $slugger;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        SluggerInterface $slugger
    ) {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $trickIndex = 0;
        $pictureIndex = 1;

        $userNames = [
            'mando',
            'vader',
            'stormy',
            'yoda',
            'R2D2',
            'jabba',
            'C3PO'
        ];

        $categoryNames = [
            'grabs',
            'rotations',
            'flips',
            'rotation désaxées',
            'slides',
            'one foot',
            'old school'
        ];

        $trickNames = [
            'nose grab',
            'tail grab',
            '180',
            '360',
            'front flip',
            'back flip',
            'corkscrew',
            'rodeo',
            'nose slide',
            'tail slide',
            'one foot tail grab',
            'one foot nose slide',
            'backside air',
            'method air'
        ];

        for ($i = 0; $i < count($userNames); $i++) {

            $newUser = new User;
            $hashedPassword = $this->hasher->hashPassword($newUser, "password");
            $manager->persist(
                $newUser
                    ->setEmail($userNames[$i] . '@gmail.com')
                    ->setPassword($hashedPassword)
                    ->setIdPhotoPath('/uploads/idPhotos/' . $userNames[$i] . '.png')
                    ->setScreenName(ucfirst($userNames[$i]))
            );

            $newCategory = new Category;
            $manager->persist(
                $newCategory
                    ->setName($categoryNames[$i])
                    ->setSlug($this->slugger->slug($categoryNames[$i]))
            );

            for ($j = 1; $j <= 2; $j++) {

                $newTrick = new Trick;
                $manager->persist(
                    $newTrick
                        ->setName($trickNames[$trickIndex])
                        ->setSlug($this->slugger->slug($trickNames[$trickIndex]))
                        ->setDescription("Courte description de comment réaliser ce 'fake' trick, histoire d'avoir quelque chose dans la description. Et aussi parce que les lorem ipsum, ça suffit !")
                        ->setCategory($newCategory)
                        ->setUser($newUser)
                        ->addPicture(
                            (new Picture())
                                ->setPath('/uploads/pictures/picture - ' . $pictureIndex . '.jpeg')
                        )
                );

                $pictureIndex++;

                $manager->persist(
                    $newTrick
                        ->addPicture(
                            (new Picture())
                                ->setPath('/uploads/pictures/picture - ' . $pictureIndex . '.jpeg')
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

                for ($c = 1; $c <= 6; $c++) {
                    $manager->persist(
                        (new Comment())
                            ->setContent("Petit commentaire inutile sur un trick n'existant pas plus que l'utilisateur qui rédige ces mots.")
                            ->setTrick($newTrick)
                            ->setUser($newUser)
                    );
                }

                $trickIndex++;
                $pictureIndex++;
            }
        }

        $manager->flush();
    }
}
