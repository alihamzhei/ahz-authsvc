<?php

namespace Src\User\Domain\Services;

use Illuminate\Auth\AuthenticationException;
use Src\Common\Infrastructure\Facades\Response;
use Src\User\Application\DTOs\UserLoginDTO;
use Src\User\Application\DTOs\UserRegisterDTO;
use Src\User\Infrastructure\Repositories\UserRepositoryInterface;
use Src\User\Infrastructure\Services\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{

    public function __construct(public readonly UserRepositoryInterface $repository)
    {
    }

    public function login(UserLoginDTO $loginDTO)
    {
        if ( ! $token = auth()->attempt([
            'email' => $loginDTO->email,
            'password' => $loginDTO->password,
        ])) {
            throw new AuthenticationException();
        }


        return Response::message('login successfully')
            ->data([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ])
            ->send();
    }

    public function register(UserRegisterDTO $registerDTO)
    {
        $user = $this->repository->create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => bcrypt($registerDTO->password),
        ]);

        return Response::message('user created successfully')
            ->data($user)
            ->send();
    }
}
