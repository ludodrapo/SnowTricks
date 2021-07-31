<?php

namespace tests\Controller;

use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SigninControllerTest extends WebTestCase
{
    public function testSuccessfullCompleteSignin()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/signin');

        $this->assertSelectorTextContains('h3', 'Pour vous inscrire ...');

        $form = $crawler->filter('form[name=signin_form]')->form();

        $csrfToken = $form->get('signin_form[_token]')->getValue();

        $formData = [
            'signin_form' => [
                '_token' => $csrfToken,
                'email' => 'testUser@gmail.com',
                'screenName' => 'testUser',
                'password' => '5nowTrick5.com',
                'agreeTerms' => 1
            ]
        ];

        $filename = (string) uniqid() . ".png";
        $path = sprintf("%s/../../public/uploads/idPhotos/%s", __DIR__, $filename);
        copy(sprintf("%s/../../public/uploads/idPhotos/user1.png", __DIR__), $path);

        $fileData = [
            'signin_form' => [
                'idPhoto' => new UploadedFile(
                    $path,
                    $filename,
                    'image/png',
                    null,
                    true
                )
            ]
        ];

        $client->request('POST', '/signin', $formData, $fileData);

        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testSuccessfullSigninWithoutUploadingAvatar()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/signin');

        $this->assertSelectorTextContains('h3', 'Pour vous inscrire ...');

        $form = $crawler->filter('form[name=signin_form]')->form();

        $csrfToken = $form->get('signin_form[_token]')->getValue();

        $formData = [
            'signin_form' => [
                '_token' => $csrfToken,
                'email' => 'testUser@gmail.com',
                'screenName' => 'testUser',
                'password' => '5nowTrick5.com',
                'agreeTerms' => 1
            ]
        ];

        $client->request('POST', '/signin', $formData);

        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorExists('div.alert.alert-success');
    }
}
