<?php

namespace Src\User\Application\DTOs;

class UserLoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}