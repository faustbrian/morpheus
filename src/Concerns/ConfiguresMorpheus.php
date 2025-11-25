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
 * Trait for Laravel service providers to configure morph key mappings from config.
 *
 * This trait provides a reusable method for reading morphKeyMap or enforceMorphKeyMap
 * configuration and applying it to the MorphKeyRegistry. Use this trait in your
 * package's service provider to enable config-based morph key mapping.
 *
 * @property Application $app
 *
 * @author Brian Faust <brian@cline.sh>
 */
trait ConfiguresMorpheus
{
    /**
     * Configure morph key mappings from the given configuration key.
     *
     * Reads morphKeyMap and enforceMorphKeyMap from the specified config prefix
     * and applies them to the MorphKeyRegistry. Validates that only one mapping
     * strategy is configured to prevent conflicting behavior.
     *
     * ```php
     * // In your service provider's boot method:
     * $this->configureMorpheus('mypackage');
     *
     * // This reads from config/mypackage.php:
     * // 'morphKeyMap' => [User::class => 'uuid'],
     * // OR
     * // 'enforceMorphKeyMap' => [User::class => 'uuid'],
     * ```
     *
     * @param string $configPrefix The config file prefix (e.g., 'lineage', 'warden')
     *
     * @throws InvalidConfigurationException When both morphKeyMap and enforceMorphKeyMap are configured
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
