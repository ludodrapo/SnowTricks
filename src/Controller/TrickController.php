<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\UrlToEmbedTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;

/**
 * class TrickController
 * @package App\Controller
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_category", priority=-1)
     */
    public function category(Category $category): Response
    {
        return $this->render('category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
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

        //transforming any url in embed video functionnal links
        //adding exception to the form_errors
        if ($form->isSubmitted()) {
            foreach ($trick->getVideos() as $k => $video) {
                try {
                    $video->setUrl($transformer->urlToEmbed($video->getUrl()));
                } catch (Exception $e) {
                    $form->get('videos')->get((string) $k)->addError(new FormError($e->getMessage()));
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug(strtolower((string) $slugger->slug($trick->getName())));
            $trick->setUser($this->getUser());

            // foreach ($trick->getPictures() as $picture) {
            //     $picture->setAlt(strtolower((string) $slugger->slug($picture->getAlt())));
            // }

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
        Request $request,
        Trick $trick,
        EntityManagerInterface $em,
        UrlToEmbedTransformer $transformer
    ): Response {

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        //transforming any url in embed video functionnal links
        //adding exception to the form_errors
        if ($form->isSubmitted()) {
            foreach ($trick->getVideos() as $k => $video) {
                try {
                    $video->setUrl($transformer->urlToEmbed($video->getUrl()));
                } catch (Exception $e) {
                    $form->get('videos')->get((string) $k)->addError(new FormError($e->getMessage()));
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // uncomment this if you want to change the trick slug when name is changed
            // $trick->setSlug(strtolower($slugger->slug($trick->getName())));

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
     */
    public function delete(
        Trick $trick,
        EntityManagerInterface $em,
        Filesystem $filesystem
    ): Response {

        $category_slug = $trick->getCategory()->getSlug();

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
