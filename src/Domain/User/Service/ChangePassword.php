<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class ChangePassword
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param UserRepository $repository The repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    public function changePassword($username, $password): int
    {

        $res = $this->repository->changePassword($username, $password);

        //$this->logger->info(sprintf('Login done'));

        return $res;
    }

}