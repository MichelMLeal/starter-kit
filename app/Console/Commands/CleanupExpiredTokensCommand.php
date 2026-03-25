<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

final class CleanupExpiredTokensCommand extends Command
{
    protected $signature = 'auth:cleanup-tokens';

    protected $description = 'Remove expired refresh tokens and personal access tokens';

    public function handle(RefreshTokenRepositoryInterface $refreshTokenRepository): int
    {
        $refreshDeleted = $refreshTokenRepository->deleteExpired();
        $this->info("Deleted {$refreshDeleted} expired refresh tokens.");

        $accessDeleted = PersonalAccessToken::where('expires_at', '<', now())->delete();
        $this->info("Deleted {$accessDeleted} expired personal access tokens.");

        return Command::SUCCESS;
    }
}
