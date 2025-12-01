<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when Morpheus configuration contains invalid or conflicting settings.
 *
 * Indicates configuration errors that must be resolved before the package can function
 * correctly, such as conflicting mapping strategies or malformed configuration values.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidConfigurationException extends InvalidArgumentException
{
    /**
     * Creates exception for simultaneous morphKeyMap and enforceMorphKeyMap configuration.
     *
     * These mapping strategies are mutually exclusive: morphKeyMap provides optional
     * mappings with fallback to model defaults, while enforceMorphKeyMap requires
     * explicit mappings for all models. Using both creates ambiguous behavior that
     * must be resolved by choosing one strategy.
     *
     * @return self Exception instance describing the configuration conflict
     */
    public static function conflictingMorphKeyMaps(): self
    {
        return new self(
            'Cannot configure both "morphKeyMap" and "enforceMorphKeyMap" simultaneously. '.
            'Choose one: use "morphKeyMap" for optional mapping or "enforceMorphKeyMap" for strict enforcement.',
        );
    }
}
