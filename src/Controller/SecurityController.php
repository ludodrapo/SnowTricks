<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Form\SigninFormType;
use App\Service\FileUploader;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\UpdatePasswordFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\GuardAuthenticationFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/profile/{id}", name="security_profile")
     * @param int $id
     * @param UserRepository $userRepository
     * @return Response
     */
    public function profile(
        $id,
        UserRepository $userRepository,
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {

        $user = $userRepository->find($id);
        if (!$user) {
            $this->createNotFoundException("Aucun profil n'existe avec cet identifiant.");
        }

        $form = $this->createForm(UpdatePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('oldPassword')->getData();
            $new_password_1 = $form->get('newPassword1')->getData();
            $new_password_2 = $form->get('newPassword2')->getData();
            if (!$hasher->isPasswordValid($user, $old_password)) {
                $this->addFlash('danger', "Attention, le mot de passe actuel saisi n'est pas celui associé à ce profil.");
            } else if ($new_password_1 !== $new_password_2) {
                $this->addFlash('danger', "Attention, les deux nouveaux mots de passe ne sont pas identiques.");
            } else {
                $em->persist($user->setPassword($hasher->hashPassword($user, $new_password_1)));
                $em->flush();
                $this->addFlash('success', "Votre mot de passe a bien été modifié.");
                $this->redirectToRoute('security_profile', [
                    'id' => $user->getId()
                ]);
            }
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $utils
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @param MailerInterface $mailer
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function login(
        AuthenticationUtils $utils,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        UserPasswordHasherInterface $hasher
    ): Response {

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        // get the login error if there is one
        $error = $utils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $utils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'formView' => $form->createView()
        ]);
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
            } else if (!$idPhotoFile) {
                $user->setIdPhotoPath('/assets/img/basic-avatar.png');
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Félicitations, votre inscription est validée. Vous pouvez maintenant vous connecter.");

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
    
    /**
     * @Route("/forgotten-password", name="security_forgottenPassword")
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {

        $email = $request->request->get('reset_password_form')['email'];

        $user = $userRepository->findOneBy([
            'email' => $email
        ]);

        if (!$user) {
            $this->createNotFoundException("Aucun profil n'existe avec cet identifiant.");
        }

        $temp_password = uniqid();

        $em->persist($user->setPassword($hasher->hashPassword($user, $temp_password)));
        $em->flush();

        $email = new TemplatedEmail();
        $email
            ->from(new Address("contact@snowtricks.com", "Réinitialisation de votre mot de passe."))
            ->to($user->getEmail())
            ->htmlTemplate('emails/resetPasswordEmail.html.twig')
            ->context([
                'user' => $user,
                'temp_password' => $temp_password
            ])
            ->subject("Réinitialisation de votre mot de passe.");

        $mailer->send($email);

        $this->addFlash('success', "Votre mot de passe a bien été réinitialisé. Vous allez recevoir un email avec un mot de passe temporaire.");

        return $this->redirectToRoute('security_login');
    }
}
