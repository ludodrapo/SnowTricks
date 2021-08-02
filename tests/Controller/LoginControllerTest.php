<?php

namespace tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginControllerTest
 * @package tests\Controller
 */
class LoginControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testDisplaysLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h3', 'Pour vous connecter ...');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
     * @return void
     */
    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);

        $form = $crawler->selectButton('C\'est parti !')->form([
            'email' => $testUser->getEmail(),
            'password' => 'wrongPassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfullLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);

        $form = $crawler->selectButton('C\'est parti !')->form();

        $form['email'] = $testUser->getEmail();
        $form['password'] = 'password';

        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('h3', 'Bienvenue ' . ucfirst($testUser->getScreenName()) . ' !');
    }

    public function testSuccessfullLogout()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Se déconnecter')->link();

        $client->click($link);
        $client->followRedirect();
        $this->assertRouteSame('home');
    }
}
