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
 * Bootstraps the Morpheus package by registering the MorphKeyRegistry singleton,
 * merging configuration, registering Blueprint macros, and enabling configuration
 * publishing. Other packages can integrate Morpheus by either using this provider
 * or including the ConfiguresMorpheus trait in their own service providers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MorpheusServiceProvider extends ServiceProvider
{
    use ConfiguresMorpheus;

    /**
     * Registers package services into the Laravel container.
     *
     * Merges the package configuration file with the application's config and
     * registers the MorphKeyRegistry as a singleton to ensure consistent key
     * resolution throughout the application lifecycle.
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
     * Bootstraps package services after registration.
     *
     * Enables configuration publishing for console environments, registers
     * Blueprint macros for migration convenience methods, and configures
     * the MorphKeyRegistry based on the merged configuration settings.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerBlueprintMacros();
        $this->configureMorpheus('morpheus');
    }

    /**
     * Registers Blueprint macros for polymorphic column creation.
     *
     * Adds morpheus() and nullableMorpheus() macros to Laravel's Blueprint class,
     * enabling type-aware polymorphic column creation in migrations based on the
     * configured default morph type.
     */
    private function registerBlueprintMacros(): void
    {
        BlueprintMacros::register();
    }

    /**
     * Registers publishable configuration assets for the package.
     *
     * Enables developers to publish the package configuration file to their
     * application's config directory using the php artisan vendor:publish command
     * with the 'morpheus-config' tag. Only registered when running in console mode.
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
