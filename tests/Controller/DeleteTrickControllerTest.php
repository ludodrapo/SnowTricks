<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteTrickControllerTest extends WebTestCase
{
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
