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


    public function loginUser($username, $password): Array
    {
        $res = $this->repository->login($username, $password);
        return $res;
    }

    public function unbanUser($username): int
    {
        $res = $this->repository->unban($username);
        return $res;
    }



}