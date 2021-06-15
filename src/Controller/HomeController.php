<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findAll();
        return $this->render('home.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
