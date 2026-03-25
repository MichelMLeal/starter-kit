<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Auth\Requests\RefreshTokenRequest;
use App\Application\Shared\Controllers\Controller;
use App\Domain\Auth\Actions\RefreshTokenAction;
use App\Domain\Auth\Exceptions\TokenExpiredException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class TokenController extends Controller
{
    public function __construct(
        private readonly RefreshTokenAction $refreshTokenAction,
    ) {}

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $result = $this->refreshTokenAction->execute(
                $request->validated('refresh_token'),
            );

            return response()->json([
                'message' => 'Token refreshed successfully.',
                'data' => [
                    'access_token' => $result['access_token'],
                    'refresh_token' => $result['refresh_token'],
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
