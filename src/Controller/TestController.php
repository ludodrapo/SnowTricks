<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TestController extends AbstractController
{
    /**
     * @Route("/test/{prenom?ludo}", name="test")
     */
    public function test($prenom)
    {
        $prenom = ucfirst($prenom);
        return $this->render('test.html.twig', [
            'prenom' => $prenom
        ]);
    }
}
