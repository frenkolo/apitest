<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;
use App\Exception\ValidationException;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Service.
 */
final class UserLogin
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

    /**
     *
     *
     * @param array $data The form data
     *
     * @return int The new user ID
     */
    public function loginUser($username, $password): Array
    {

        $res = $this->repository->login($username, $password);

        //$this->logger->info(sprintf('Login done'));

        return $res;
    }

}