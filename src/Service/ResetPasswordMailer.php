<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordMailer
{
    /** @var MailerInterface */
    private $mailer;

    /** @var UserPasswordHasherInterface */
    private $hasher;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        MailerInterface $mailer,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ) {
        $this->mailer = $mailer;
        $this->hasher = $hasher;
        $this->em = $em;
    }

    /**
     * To generate a temp password based on a uniqid and store it in the database
     *
     * @param User $user
     * @return string
     */
    public function resetPassword(User $user): string
    {
        $temp_password = uniqid();
        $hashed_temp_password = $this->hasher->hashPassword($user, $temp_password);
        $this->em->persist($user->setPassword($hashed_temp_password));
        $this->em->flush();

        return $temp_password;
    }

    /**
     * To generate a templated email to communicate the new password to the user
     *
     * @param User $user
     * @param string $temp_password
     * @return TemplatedEmail
     */
    public function sendResetPasswordMail($user, $temp_password): TemplatedEmail
    {
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

        $this->mailer->send($email);
        
        return $email;
    }

}
