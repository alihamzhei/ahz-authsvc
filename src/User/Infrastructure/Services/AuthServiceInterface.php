<?php

namespace Src\User\Infrastructure\Services;

use Illuminate\Support\Collection;
use Src\User\Application\DTOs\UserLoginDTO;
use Src\User\Application\DTOs\UserRegisterDTO;

interface AuthServiceInterface
{
    public function login(UserLoginDTO $loginDTO);
    public function register(UserRegisterDTO $registerDTO);
}
