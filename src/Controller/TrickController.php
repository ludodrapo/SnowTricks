<?php

namespace App\Controller;

use DateTime;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas ou plus.");
        }

        return $this->render('trick/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     * @param [string] $slug
     * @param TrickRepository $trickRepository
     * @return Response
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
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response {

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
     * @param [int] $id
     * @param Request $request
     * @param TrickRepository $trickRepository
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @return Response
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

    /**
     * @Route("/admin/trick/{id}/delete", name="trick_delete")
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(
        TrickRepository $trickRepository,
        EntityManagerInterface $em,
        $id
    ): Response {

        $em->remove($trickRepository->find($id));
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
