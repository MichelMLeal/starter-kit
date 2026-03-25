<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Auth\Resources\UserResource;
use App\Application\Shared\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }
}
