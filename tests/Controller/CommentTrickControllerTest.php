<?php

declare(strict_types=1);

namespace tests\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class CommentTrickControllerTest
 * @package tests\Controller
 */
class CommentTrickControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testSuccessfullCommentOneTrick()
    {
        $client = $this->createClient();
        // To access the create page, we need to log a test user in
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);
        // Now we need an actual trick page
        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $crawler = $client->request(
            'GET',
            '/' . $trick->getCategory()->getSlug() . '/' . $trick->getSlug()
        );

        $form = $crawler->selectButton('Je valide ce commentaire')->form();

        $form['comment[content]'] = "Essai de commentaire pour test fonctionnel phpunit";

        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
