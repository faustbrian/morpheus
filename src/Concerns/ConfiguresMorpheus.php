<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Concerns;

use Cline\Morpheus\Exceptions\InvalidConfigurationException;
use Cline\Morpheus\MorphKeyRegistry;
use Illuminate\Contracts\Foundation\Application;

use function config;
use function is_array;

/**
 * Configures morph key mappings from Laravel configuration files.
 *
 * Provides a reusable method for service providers to read morph key configuration
 * and apply it to the MorphKeyRegistry. Supports both optional mapping (morphKeyMap)
 * and strict enforcement (enforceMorphKeyMap) strategies for polymorphic relationships.
 *
 * ```php
 * class YourServiceProvider extends ServiceProvider
 * {
 *     use ConfiguresMorpheus;
 *
 *     public function boot(): void
 *     {
 *         $this->configureMorpheus('mypackage');
 *     }
 * }
 * ```
 *
 * @property Application $app Laravel application instance required by trait
 *
 * @author Brian Faust <brian@cline.sh>
 */
trait ConfiguresMorpheus
{
    /**
     * Configures morph key mappings from the specified configuration file.
     *
     * Reads morphKeyMap and enforceMorphKeyMap arrays from the given configuration
     * prefix and applies them to the MorphKeyRegistry singleton. Validates that
     * only one mapping strategy is configured, as these are mutually exclusive.
     *
     * The morphKeyMap strategy allows unmapped models to fall back to their default
     * key name, while enforceMorphKeyMap requires all models to have explicit mappings.
     *
     * ```php
     * // In your service provider's boot method:
     * $this->configureMorpheus('mypackage');
     *
     * // This reads from config/mypackage.php:
     * // 'morphKeyMap' => [User::class => 'uuid', Seller::class => 'ulid'],
     * // OR
     * // 'enforceMorphKeyMap' => [User::class => 'uuid', Seller::class => 'ulid'],
     * ```
     *
     * @param string $configPrefix Configuration file prefix without .php extension (e.g., 'ancestry', 'warden', 'morpheus')
     *
     * @throws InvalidConfigurationException When both morphKeyMap and enforceMorphKeyMap are configured simultaneously
     */
    protected function configureMorpheus(string $configPrefix): void
    {
        /** @var mixed $morphKeyMap */
        $morphKeyMap = config($configPrefix.'.morphKeyMap', []);

        /** @var mixed $enforceMorphKeyMap */
        $enforceMorphKeyMap = config($configPrefix.'.enforceMorphKeyMap', []);

        if (!is_array($morphKeyMap)) {
            $morphKeyMap = [];
        }

        if (!is_array($enforceMorphKeyMap)) {
            $enforceMorphKeyMap = [];
        }

        $hasMorphKeyMap = $morphKeyMap !== [];
        $hasEnforceMorphKeyMap = $enforceMorphKeyMap !== [];

        // Validate that only one mapping strategy is configured to prevent conflicting behavior
        if ($hasMorphKeyMap && $hasEnforceMorphKeyMap) {
            throw InvalidConfigurationException::conflictingMorphKeyMaps();
        }

        /** @var MorphKeyRegistry $registry */
        $registry = $this->app->make(MorphKeyRegistry::class);

        if ($hasEnforceMorphKeyMap) {
            /** @var array<class-string, string> $enforceMorphKeyMap */
            $registry->enforce($enforceMorphKeyMap);
        } elseif ($hasMorphKeyMap) {
            /** @var array<class-string, string> $morphKeyMap */
            $registry->map($morphKeyMap);
        }
    }
}
