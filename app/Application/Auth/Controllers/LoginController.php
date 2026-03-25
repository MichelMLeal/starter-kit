<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Auth\Requests\LoginRequest;
use App\Application\Auth\Resources\UserResource;
use App\Application\Shared\Controllers\Controller;
use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginAction->execute(
                new LoginDTO(
                    email: $request->validated('email'),
                    password: $request->validated('password'),
                ),
            );

            return response()->json([
                'message' => 'Login successful.',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'access_token' => $result['access_token'],
                    'refresh_token' => $result['refresh_token'],
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
