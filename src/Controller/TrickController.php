<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use App\Repository\TrickRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TrickController extends AbstractController
{
    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     */
    public function show($slug, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$trick) {
            throw $this->createNotFoundException("Désolé, ce trick n'existe pas ou plus.");
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("admin/trick/create", name="trick_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $trick = new Trick;

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $date = new DateTime();
            $trick->setCreationDate($date);
            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/trick/{id}/edit", name="trick_edit")
     */
    public function edit(
        $id,
        Request $request,
        TrickRepository $trickRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response {
        $trick = $trickRepository->find($id);

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setSlug(strtolower($slugger->slug($trick->getName())));

            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('trick/edit.html.twig', [
            'formView' => $formView,
            'trick' => $trick
        ]);
    }
}
