<?php

namespace App\DataFixtures;

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

/**
 * class AppFixtures
 * @package App\DataFixtures
 */
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
            'Mando',
            'Vader',
            'Stormy',
            'Yoda',
            'R2D2',
            'Jabba',
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

        $descriptions = [
            "On commence tout simplement avec LE trick. 
            Les mauvaises langues prétendent qu’un backside air 
            suffit à reconnaître ceux qui savent snowboarder. 
            Si c’est vrai, alors Nicolas Müller est le meilleur 
            snowboardeur du monde. Personne ne sait s’étirer aussi joliment, 
            ne demeure aussi zen, n’est aussi provocant dans la jouissance.",
            "Bode Merril est la preuve vivante que la réincarnation 
            n’est pas un conte de fée. Dans sa vie antérieure de flamant rose, 
            il avait déjà l’habitude d’affronter le quotidien sur une patte. 
            Quelque 200 ans plus tard, il a eu la chance d’être un homme doté d’un snowboard, 
            ce qui a fini par donner à son être l’élan nécessaire. 
            Il aime bien s’avaler quelques one foot double backflips au p’tit déj."
        ];

        for ($i = 0; $i < count($userNames); $i++) {

            $newUser = new User;
            $hashedPassword = $this->hasher->hashPassword($newUser, "password");
            $manager->persist(
                $newUser
                    ->setEmail($userNames[$i] . '@gmail.com')
                    ->setPassword($hashedPassword)
                    ->setIdPhotoPath('/uploads/idPhotos/' . $userNames[$i] . '.png')
                    ->setScreenName($userNames[$i])
            );

            $newCategory = new Category;
            $manager->persist(
                $newCategory
                    ->setName($categoryNames[$i])
                    ->setSlug($this->slugger->slug($categoryNames[$i]))
            );

            for ($j = 0; $j <= 1; $j++) {

                $newTrick = new Trick;
                $manager->persist(
                    $newTrick
                        ->setName($trickNames[$trickIndex])
                        ->setSlug($this->slugger->slug($trickNames[$trickIndex]))
                        ->setDescription($descriptions[$j])
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
                        ->addVideo(
                            (new Video())
                                ->setUrl("https://www.youtube.com/embed/OsbpD8BN10k")
                        )
                );

                for ($c = 1; $c <= 6; $c++) {
                    $manager->persist(
                        (new Comment())
                            ->setContent("Petit commentaire inutile sur un trick n'existant pas plus que l'utilisateur censé avoir rédigé ces mots.")
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
