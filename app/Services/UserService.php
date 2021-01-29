<?php


namespace App\Services;


use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\UserRepositoryEloquent;

class UserService
{
    /**
     * @var UserRepositoryEloquent
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


}
