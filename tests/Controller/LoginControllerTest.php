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

    /**
     * @return void
     */
    public function testTryingToResetPasswordWhileNotRegisteredRedirectsToSignin()
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('security_login'));

        $form = $crawler->selectButton('Réinitialiser')->form();
        $form['reset_password_form[email]'] = 'unknownEmail@gmail.com';

        $client->submit($form);

        $this->assertEmailCount(0);
        $this->assertResponseRedirects('/signin');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfullResetPassword()
    {
        $client = static::createClient();

        // Another simple but efficient method :
        // $userRepository = static::getContainer()->get(UserRepository::class);
        // $testUser = $userRepository->findOneBy([]);
        // $crawler = $client->request('GET', '/login');

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router.default');

        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');

        /** @var User $testUser */
        $testUser = $em->getRepository(User::class)->findOneBy([]);

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('security_login'));

        $form = $crawler->selectButton('Réinitialiser')->form();
        $form['reset_password_form[email]'] = $testUser->getEmail();

        $client->submit($form);

        $this->assertEmailCount(1);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
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
