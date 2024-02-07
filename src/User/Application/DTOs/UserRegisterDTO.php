<?php

namespace Src\User\Application\DTOs;

class UserRegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}