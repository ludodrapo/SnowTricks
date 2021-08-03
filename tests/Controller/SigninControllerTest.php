<?php

declare(strict_types=1);

namespace tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * class SigninControllerTest
 * @package tests\Controller
 */
class SigninControllerTest extends WebTestCase
{
    /**
     * @return void
     */
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

        $filename = 'idPhotoTest.png';
        $path = sprintf("%s/../../public/uploads/idPhotos/%s", __DIR__, $filename);
        copy(sprintf("%s/../../public/uploads/idPhotos/mando.png", __DIR__), $path);

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

    /**
     * @return void
     */
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
