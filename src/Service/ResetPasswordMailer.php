<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Service\ResetPassword;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * class ResetPasswordMailer
 * @package App\Service
 */
class ResetPasswordMailer
{
    /** @var MailerInterface $mailer */
    private $mailer;

    /** @var  ResetPassword $resetter */
    private $resetter;

    public function __construct(
        MailerInterface $mailer,
        ResetPassword $resetter
    ) {
        $this->mailer = $mailer;
        $this->resetter = $resetter;
    }

    /**
     * To generate a templated email to send the new password to the user
     * 
     * @param User $user
     * @return TemplatedEmail
     */
    public function sendResetPasswordMail($user): TemplatedEmail
    {
        $temp_password = $this->resetter->resetPassword($user);

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
