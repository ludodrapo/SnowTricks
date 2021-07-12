<?php

namespace App\Controller;

use App\Entity\Category;
use DateTime;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\UrlToEmbedTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_category", priority=-1)
     * @param Category $category
     */
    public function category(Category $category): Response
    {
        return $this->render('trick/category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function show(
        Trick $trick,
        Request $request,
        EntityManagerInterface $em,
        CommentRepository $commentRepository,
        PaginatorInterface $paginator
    ): Response {

        $comments_data = $commentRepository->findBy(['trick' => $trick], ['creationDate' => 'DESC']);

        $comments = $paginator->paginate(
            $comments_data,
            $request->query->getInt('page', 1),
            5
        );

        if (!$trick) {
            throw $this->createNotFoundException("Désolé, ce trick n'existe pas ou plus.");
        }

        $comment = new Comment;
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', "Merci pour votre commentaire, " . ucfirst($this->getUser()->getScreenName()) . " !");

            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'commentFormView' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("admin/trick/create", name="trick_create")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @param UrlToEmbedTransformer $transformer
     * @return Response
     */
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        UrlToEmbedTransformer $transformer
    ): Response {

        $trick = new Trick;
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $trick->setUser($this->getUser());

            foreach ($trick->getVideos() as $video) {
                $video->setUrl($transformer->urlToEmbed($video->getUrl()));
            }

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
     * @param UrlToEmbedTransformer $transformer
     * @return Response
     */
    public function edit(
        $id,
        Request $request,
        TrickRepository $trickRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        UrlToEmbedTransformer $transformer
    ): Response {
        $trick = $trickRepository->find($id);

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $trick->setUser($this->getUser());

            foreach ($trick->getVideos() as $video) {
                $video->setUrl($transformer->urlToEmbed($video->getUrl()));
            }
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
     *
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $em
     * @param [type] $id
     * @param Filesystem $filesystem
     * @return Response
     */
    public function delete(
        TrickRepository $trickRepository,
        EntityManagerInterface $em,
        $id,
        Filesystem $filesystem
    ): Response {

        $trick = $trickRepository->find($id);
        $category_slug = $trick->getCategory()->getSlug();

        if (!$trick) {
            $this->addFlash('danger', "Ce trick n'existe pas ou plus.");
            $this->redirectToRoute('home');
        }

        $pictures = $trick->getPictures();
        foreach ($pictures as $picture) {
            $filesystem->remove($picture->getPath());
        }

        $em->remove($trick);
        $em->flush();

        $this->addFlash('success', "Le trick a bien été supprimé, " . ucfirst($this->getUser()->getScreenName()));

        return $this->redirectToRoute('trick_category', [
            'slug' => $category_slug
        ]);
    }
}
