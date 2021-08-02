<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class EditTrickControllerTest extends WebTestCase
{
    public function testTryingToAccessEditTrickPageWhileNotLoggedInRedirectsToLogin()
    {
        $client = $this->createClient();

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->request('GET', '/admin/trick/' . $trick->getId() . '/edit');
        $this->assertResponseRedirects('/login');
    }

    public function testSuccessfullEditOneTrick()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $testTrick = $trickRepository->findOneBy([]);

        $crawler = $client->request('GET', 'admin/trick/' . $testTrick->getId() . '/edit');

        $form = $crawler->selectButton('Enregistrer les modifications')->form();

        $csrfToken = $form->get('trick[_token]')->getValue();

        $formData = [
            'trick' => [
                '_token' => $csrfToken,
                'name' => 'Test de modification de trick',
                'description' => 'Modification de la description toujours très courte mais juste assez longue pour passer les constraints.',
                'category' => 2,
                'videos' => [
                    ['url' => 'https://www.youtube.com/watch?v=_DCkjHact1M']
                ]
            ]
        ];

        $filename = 'testUpdatePicture.jpeg';
        $path = sprintf("%s/../../public/uploads/pictures/%s", __DIR__, $filename);
        copy(sprintf("%s/../../public/uploads/pictures/picture - 2.jpeg", __DIR__), $path);

        $fileData = [
            'trick' => [
                'pictures' => [
                    [],
                    [],
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

        $client->request(Request::METHOD_POST, '/admin/trick/' . $testTrick->getId() . '/edit', $formData, $fileData);
        $client->followRedirect();

        $this->assertSelectorTextContains('h2', 'Test de modification de trick');
    }
}
