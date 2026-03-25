<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Auth\Requests\RegisterRequest;
use App\Application\Auth\Resources\UserResource;
use App\Application\Shared\Controllers\Controller;
use App\Domain\Auth\Actions\RegisterAction;
use App\Domain\Auth\DTOs\RegisterDTO;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterAction $registerAction,
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->registerAction->execute(
                new RegisterDTO(
                    name: $request->validated('name'),
                    email: $request->validated('email'),
                    password: $request->validated('password'),
                ),
            );

            return response()->json([
                'message' => 'User registered successfully.',
                'data' => new UserResource($user),
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Unique') || str_contains($e->getMessage(), 'duplicate')) {
                return response()->json([
                    'message' => 'The email has already been taken.',
                    'errors' => ['email' => ['The email has already been taken.']],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            throw $e;
        }
    }
}
