<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninFormType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\GuardAuthenticationFactory;

class SecurityController extends AbstractController
{

    /**
     * @Route("/profile/{id}", name="security_profile")
     * @param int $id
     * @param UserRepository $userRepository
     * @return Response
     */
    public function profile($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            $this->createNotFoundException("Aucun profil n'existe avec cet identifiant.");
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/signin", name="security_signin")
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $em
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function signin(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
        FileUploader $fileUploader
    ): Response {
        $user = new User;
        $form = $this->createForm(SigninFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //upload the avatar image
            /** @var UploadedFile $file */
            $idPhotoFile = $form->get('idPhoto')->getData();
            if ($idPhotoFile) {
                $idPhotoFileName = $fileUploader->upload($idPhotoFile);
                $user->setIdPhotoPath('/' . $fileUploader->getTargetDirectory() . '/' . $idPhotoFileName);
            }

            $em->persist($user);
            $em->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', "Votre inscription est validée, félicitations.");

            return $this->redirectToRoute('home');
        }

        return $this->render('security/signin.html.twig', [
            'signinFormView' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
