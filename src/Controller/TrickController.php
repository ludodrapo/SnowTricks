<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/show", name="trick_show")
     */
    public function show()
    {
        return $this->render('show.html.twig');
    }
}
