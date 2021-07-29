<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninFormType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\UpdatePasswordFormType;
use App\Service\ResetPasswordMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/profile", name="security_profile")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function profile(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {

        $user = $this->getUser();
        $form = $this->createForm(UpdatePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new_password = $form->get('password')->getData();

            $em->persist($user->setPassword($hasher->hashPassword($user, $new_password)));
            $em->flush();
            $this->addFlash('success', "Votre mot de passe a bien été modifié.");
            $this->redirectToRoute('security_profile');
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
     * @return Response
     */
    public function login(
        AuthenticationUtils $utils,
        Request $request
    ): Response {

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        $error = $utils->getLastAuthenticationError();
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
                    $form->get('password')->getData()
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
     * @return Response
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordMailer $resetPasswordMailer
    ): Response {

        $user = $userRepository->findOneBy(['email' => $request->request->get('reset_password_form')['email']]);

        if (!$user) {
            $this->addFlash('danger', "Aucun profil n'est associé à cet email, mais vous pouvez vous inscrire sur cette page.");

            return $this->redirectToRoute('security_signin');
        } else {
            $resetPasswordMailer->sendResetPasswordMail($user);
            $this->addFlash('success', "Votre mot de passe a bien été réinitialisé. Vous allez recevoir un email avec un mot de passe temporaire. Veillez à le modifier le plus vite possible dans votre profil.");

            return $this->redirectToRoute('security_login');
        }
    }
}
