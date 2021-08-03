<?php

declare(strict_types=1);

namespace tests\Controller;

use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class ReadTrickControllerTest
 * @package tests\Controller
 */
class ReadTrickControllerTest extends WebTestCase
{
    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testReadOneExistingTrickReturningOk()
    {
        $client = $this->createClient();

        $trickRepository = static::getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->request('GET', '/' . $trick->getCategory()->getSlug() . '/' . $trick->getSlug());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', $trick->getName());
    }
}