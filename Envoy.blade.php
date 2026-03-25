@servers(['production' => env('DEPLOY_USER') . '@' . env('DEPLOY_HOST')])

@setup
    $repository = 'git@github.com:MichelMLeal/starter-kit.git';
    $appDir = env('DEPLOY_PATH', '/var/www/starter-kit');
    $releasesDir = $appDir . '/releases';
    $currentDir = $appDir . '/current';
    $sharedDir = $appDir . '/shared';
    $release = date('Y-m-d_H-i-s');
    $releaseDir = $releasesDir . '/' . $release;
    $branch = env('DEPLOY_BRANCH', 'main');
    $keepReleases = 5;
@endsetup

@story('deploy')
    clone
    shared
    install
    build
    migrate
    optimize
    activate
    cleanup
    health-check
@endstory

@story('rollback')
    rollback-release
    optimize
@endstory

{{-- Clone the repository --}}
@task('clone')
    echo "🚀 Cloning {{ $branch }} into {{ $releaseDir }}..."
    mkdir -p {{ $releaseDir }}
    git clone --depth 1 --branch {{ $branch }} {{ $repository }} {{ $releaseDir }}
@endtask

{{-- Link shared files and directories --}}
@task('shared')
    echo "🔗 Linking shared resources..."
    mkdir -p {{ $sharedDir }}/storage
    mkdir -p {{ $sharedDir }}/node_modules

    {{-- Remove release storage and link to shared --}}
    rm -rf {{ $releaseDir }}/storage
    ln -s {{ $sharedDir }}/storage {{ $releaseDir }}/storage

    {{-- Link .env --}}
    ln -s {{ $sharedDir }}/.env {{ $releaseDir }}/.env

    {{-- Link node_modules for faster builds --}}
    ln -s {{ $sharedDir }}/node_modules {{ $releaseDir }}/node_modules

    {{-- Ensure storage structure exists --}}
    mkdir -p {{ $sharedDir }}/storage/app/public
    mkdir -p {{ $sharedDir }}/storage/framework/{cache,sessions,testing,views}
    mkdir -p {{ $sharedDir }}/storage/logs
@endtask

{{-- Install PHP and Node dependencies --}}
@task('install')
    echo "📦 Installing dependencies..."
    cd {{ $releaseDir }}
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    npm ci --production
@endtask

{{-- Build frontend assets --}}
@task('build')
    echo "🏗️ Building frontend assets..."
    cd {{ $releaseDir }}
    npx vite build
@endtask

{{-- Run database migrations --}}
@task('migrate')
    echo "🗃️ Running migrations..."
    cd {{ $releaseDir }}
    php artisan migrate --force
@endtask

{{-- Cache config, routes, views --}}
@task('optimize')
    echo "⚡ Optimizing application..."
    cd {{ $releaseDir ?? $currentDir }}
    php artisan optimize
    php artisan view:cache
@endtask

{{-- Swap the symlink to the new release --}}
@task('activate')
    echo "✅ Activating release {{ $release }}..."
    ln -sfn {{ $releaseDir }} {{ $currentDir }}

    {{-- Reload Octane if running --}}
    if pgrep -f "octane" > /dev/null; then
        cd {{ $currentDir }}
        php artisan octane:reload
        echo "♻️ Octane reloaded"
    fi

    {{-- Restart Horizon if running --}}
    if pgrep -f "horizon" > /dev/null; then
        cd {{ $currentDir }}
        php artisan horizon:terminate
        echo "♻️ Horizon terminated (supervisor will restart)"
    fi

    {{-- Restart Reverb if running --}}
    if pgrep -f "reverb" > /dev/null; then
        cd {{ $currentDir }}
        php artisan reverb:restart
        echo "♻️ Reverb restarted"
    fi
@endtask

{{-- Remove old releases, keeping only the last N --}}
@task('cleanup')
    echo "🧹 Cleaning old releases (keeping last {{ $keepReleases }})..."
    cd {{ $releasesDir }}
    ls -dt */ | tail -n +{{ $keepReleases + 1 }} | xargs -r rm -rf
@endtask

{{-- Verify the deployment is healthy --}}
@task('health-check')
    echo "🏥 Running health check..."
    cd {{ $currentDir }}
    php artisan about --json | head -c 200
    echo ""
    echo "✅ Deploy complete!"
@endtask

{{-- Rollback to previous release --}}
@task('rollback-release')
    echo "⏪ Rolling back..."
    cd {{ $releasesDir }}
    PREVIOUS=$(ls -dt */ | head -2 | tail -1 | tr -d '/')
    if [ -z "$PREVIOUS" ]; then
        echo "❌ No previous release found"
        exit 1
    fi
    ln -sfn {{ $releasesDir }}/$PREVIOUS {{ $currentDir }}
    echo "✅ Rolled back to $PREVIOUS"
@endtask
