<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class CreateTrickControllerTest extends WebTestCase
{
    public function testTryingToAccessCreateTrickPageWhileNotLoggedInRedirectsToLogin()
    {
        $client = $this->createClient();

        $client->request('GET', '/admin/trick/create');

        $this->assertResponseRedirects('/login');
    }

    public function testSuccessfullCreateOneTrick()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        // $client->request('GET', 'admin/trick/create');
        // $this->assertSelectorTextContains('h2', 'Nouveau trick');

        $crawler = $client->request('GET', 'admin/trick/create');

        $form = $crawler->selectButton('Créer le trick')->form();

        $csrfToken = $form->get('trick[_token]')->getValue();

        $formData = [
            'trick' => [
                '_token' => $csrfToken,
                'name' => 'Test de trick',
                'description' => 'Description courte mais assez longue pour passer les constraints.',
                'category' => 1,
                'videos' => [
                    ['url' => 'https://www.youtube.com/embed/SQNc3VBOgEM']
                ]
            ]
        ];

        $filename = 'testPicture.jpeg';
        $path = sprintf("%s/../../public/uploads/pictures/%s", __DIR__, $filename);
        copy(sprintf("%s/../../public/uploads/pictures/picture - 1.jpeg", __DIR__), $path);

        $fileData = [
            'trick' => [
                'pictures' => [
                    ['file' => new UploadedFile(
                        $path,
                        $filename,
                        'image/png',
                        null,
                        true
                    )]
                ]
            ]
        ];

        $client->request(Request::METHOD_POST, '/admin/trick/create', $formData, $fileData);
        $client->followRedirect();

        $this->assertSelectorTextContains('h2', 'Test de trick');
    }
}
