<?php

namespace Src\User\Domain\Repositories;

use Src\Common\Domain\Repositories\BaseRepository;
use Src\User\Infrastructure\Models\User;
use Src\User\Infrastructure\Repositories\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }
}
