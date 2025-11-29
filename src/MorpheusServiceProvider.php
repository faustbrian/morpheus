<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus;

use Cline\Morpheus\Concerns\ConfiguresMorpheus;
use Cline\Morpheus\Support\BlueprintMacros;
use Illuminate\Support\ServiceProvider;
use Override;

use function config_path;

/**
 * Laravel service provider for the Morpheus package.
 *
 * Registers the MorphKeyRegistry as a singleton and provides optional
 * configuration publishing. Other packages can either use this provider
 * directly or use the ConfiguresMorpheus trait in their own providers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MorpheusServiceProvider extends ServiceProvider
{
    use ConfiguresMorpheus;

    /**
     * Register package services into the Laravel container.
     */
    #[Override()]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/morpheus.php',
            'morpheus',
        );

        $this->app->singleton(MorphKeyRegistry::class);
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerBlueprintMacros();
        $this->configureMorpheus('morpheus');
    }

    /**
     * Register Blueprint macros for migrations.
     */
    private function registerBlueprintMacros(): void
    {
        BlueprintMacros::register();
    }

    /**
     * Register publishable package resources.
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/morpheus.php' => config_path('morpheus.php'),
            ], 'morpheus-config');
        }
    }
}
