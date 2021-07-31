<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickControllerTest extends WebTestCase
{
    public function testDisplaysCategoryTricks()
    {
        $client = $this->createClient();
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $category = $categoryRepository->findOneBy([]);
        $tricksCount = count($category->getTricks());

        $crawler = $client->request('GET', '/' . $category->getSlug());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertPageTitleContains($category->getName());
        $this->assertCount($tricksCount, $crawler->filter('.card-body'));
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

    public function testCreateOneTrick()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', 'admin/trick/create');

        $form = $crawler->filter('form[name=trick]')->form();

        $csrfToken = $form->get('trick[_token]')->getValue();

        $formData = [
            'trick' => [
                '_token' => $csrfToken,
                'name' => 'Test de trick',
                'description' => 'Description courte mais assez longue pour passer les constraints.',
                'category' => 1,
                'pictures' => [],
                'videos' => [
                    'url' => 'https://www.youtube.com/embed/SQNc3VBOgEM'
                ]
            ]
        ];

        $filename = (string) uniqid() . '.jpg';
        $path = sprintf("%s/../../public/uploads/pictures/%s", __DIR__, $filename);
        copy(sprintf("%s/../../public/uploads/pictures/picture_1.jpg", __DIR__), $path);

        $fileData = [
            'trick' => [
                'pictures' => [
                    'file' => new UploadedFile(
                        $path,
                        $filename,
                        'image/png',
                        null,
                        true
                    )
                ]
            ]
        ];

        $client->request('POST', '/admin/trick/create', $formData, $fileData);

        $this->assertResponseRedirects();
    }

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
