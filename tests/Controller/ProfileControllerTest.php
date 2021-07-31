<?php

namespace tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilerControllerTest extends WebTestCase
{

    public function testAccessingProfilePageWhileNotLoggedInRedirectsToLogin()
    {
        $client = $this->createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]); //one user selected but not logged in
        $id = $testUser->getId();

        $client->request('GET', '/profile');

        $this->assertResponseRedirects('/login');
    }

    public function testProfilePageIsUpWhileUserLoggedIn()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $client->request('GET', '/profile');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', $testUser->getScreenName());
    }

    public function testUpdatePasswordReturningOk()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/profile');

        $form = $crawler->selectButton('Je modifie mon mot de passe')->form();

        $form['update_password_form[oldPassword]'] = 'password';
        $form['update_password_form[password][first]'] = '5nowTrick5.com';
        $form['update_password_form[password][second]'] = '5nowTrick5.com';

        $client->submit($form);
        $this->assertSelectorTextContains('div.alert.alert-success', 'Votre mot de passe a bien été modifié.');
    }
}
