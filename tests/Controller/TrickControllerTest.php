<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testDisplaysCategoryTricks()
    {
        $client = $this->createClient();
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $category = $categoryRepository->findOneBy([]);

        $client->request('GET', '/' . $category->getSlug());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertPageTitleContains($category->getName());
    }

    public function testReadOneExistingTrickReturninnOk()
    {
        $client = $this->createClient();

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->request('GET', '/' . $trick->getCategory()->getSlug() . '/' . $trick->getSlug());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', $trick->getName());
    }

    public function testCommentOneTrick()
    {
        $client = $this->createClient();
        // To access the create page, we need to log a test user in
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);
        // Now we need an actual trick page
        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $crawler = $client->request('GET', '/' . $trick->getCategory()->getSlug() . '/' . $trick->getSlug());

        $form = $crawler->selectButton('Je valide ce commentaire')->form();

        $form['comment[content]'] = "Essai de commentaire pour test fonctionnel phpunit";

        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    // NOT OK FOR NOW
    // public function testCreateOneTrick()
    // {
    //     $client = $this->createClient();
    //     // To access the create page, we need to log a test user in
    //     $userRepository = static::getContainer()->get(UserRepository::class);
    //     $testUser = $userRepository->findOneBy([]);
    //     $client->loginUser($testUser);
    //     // To access the form with the submit button node
    //     $crawler = $client->request('GET', 'admin/trick/create');
    //     $buttonCrawlerNode = $crawler->selectButton('Créer le trick');
    //     $form = $buttonCrawlerNode->form();

    //     $form['trick[name]'] = 'Nouveau trick';
    //     $form['trick[category]']->select('1');
    //     $form['trick[description]'] = 'Courte description mais suffisament longue pour que ça passe les constraints !';

    //     $picturesButton = $crawler->selectButton('Ajouter une image');
    //     $pictureForm = $picturesButton->form();
    //     $pictureForm['trick[pictures][0][file]']->upload('ludo/desktop/snowtricks-picture-test.jpg');

    //     $videosButton = $crawler->selectButton('Ajouter une video');
    //     $videoForm = $videosButton->form();
    //     $videoForm['trick[videos][0][url]'] = 'https://youtu.be/AzJPhQdTRQQ';

    //     $client->submit($form);
    //     $client->followRedirect();

    //     $this->assertSelectorExists('.alert.alert-success');
    // }

    public function testEditTrickPageIsUpWhileUserLoggedIn()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->request('GET', '/admin/trick/' . $trick->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testAccessingEditTrickPageWhileNotLoggedInRedirectsToLogin()
    {
        $client = $this->createClient();

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->request('GET', '/admin/trick/' . $trick->getId() . '/edit');
        $this->assertResponseRedirects('/login');
    }

    public function testAccessingCreateTrickPageWhileNotLoggedInRedirectsToLogin()
    {
        $client = $this->createClient();

        $client->request('GET', '/admin/trick/create');

        $this->assertResponseRedirects('/login');
    }

    public function testSuccessfullDeleteOneTrick()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $crawler = $client->request('GET', '/admin/trick/' . $trick->getId() . '/edit');

        $link = $crawler->selectLink('Supprimer définitivement')->link();
        $client->click($link);
        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }
}
