<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninFormType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\GuardAuthenticationFactory;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

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
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        // get the login error if there is one
        $error = $utils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $utils->getLastUsername();

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
        FileUploader $fileUploader,
        MailerInterface $mailer
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

            $userEmailAddress = $user->getEmail();
            $userName = $user->getScreenName();
            $email = new TemplatedEmail();
            $email
                ->from(new Address("contact@snowtricks.com", "Validation de votre inscription."))
                ->to($userEmailAddress)
                ->text("Votre inscription est validée " . $userName . ", vous pouvez maintenant vous connecter.")
                ->htmlTemplate('emails/registration.html.twig')
                ->context([
                    'user' => $user
                ])
                ->subject("Validation d'inscription");
            $mailer->send($email);

            $this->addFlash('success', "Votre inscription est validée, un email vient de vous être envoyé.");

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
