<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Shared\Controllers\Controller;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutController extends Controller
{
    public function __construct(
        private readonly LogoutAction $logoutAction,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->logoutAction->execute($user);

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
