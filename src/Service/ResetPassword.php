<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * class ResetPassword
 * @package App\Service
 */
class ResetPassword
{
    /** @var UserPasswordHasherInterface $hasher */
    private $hasher;

    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $em)
    {
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
}
