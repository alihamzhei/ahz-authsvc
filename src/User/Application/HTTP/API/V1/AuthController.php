<?php

namespace Src\User\Application\HTTP\API\V1;

use Illuminate\Http\JsonResponse;
use Src\Common\Infrastructure\Controllers\Controller;
use Src\User\Application\DTOs\UserLoginDTO;
use Src\User\Application\DTOs\UserRegisterDTO;
use Src\User\Application\Requests\API\V1\LoginRequest;
use Src\User\Application\Requests\API\V1\RegisterRequest;
use Src\User\Infrastructure\Services\AuthServiceInterface;

class AuthController extends Controller
{
    /**
     * @param AuthServiceInterface $authService
     */
    public function __construct(public AuthServiceInterface $authService)
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register',
            ],
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $loginDTO = new UserLoginDTO(
            $request->email,
            $request->password,
        );

        return $this->authService->login($loginDTO);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userRegisterDTO = new UserRegisterDTO(
            $request->name,
            $request->email,
            $request->password,
        );

        return $this->authService->register($userRegisterDTO);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
