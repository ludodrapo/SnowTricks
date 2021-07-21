<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilerControllerTest extends WebTestCase
{
    public function testProfileWhileUserLoggedIn()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@gmail.com');
        $client->loginUser($testUser);
        $id = $testUser->getId();
        $client->request('GET', '/profile/' . $id);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', 'user1');
    }
}
