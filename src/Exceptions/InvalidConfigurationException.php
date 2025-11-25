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
 * Exception thrown when morpheus configuration is invalid.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidConfigurationException extends InvalidArgumentException
{
    /**
     * Thrown when both morphKeyMap and enforceMorphKeyMap are configured simultaneously.
     * These options are mutually exclusive: morphKeyMap allows unmapped models to fall back
     * to their default key, while enforceMorphKeyMap requires strict mapping.
     */
    public static function conflictingMorphKeyMaps(): self
    {
        return new self(
            'Cannot configure both "morphKeyMap" and "enforceMorphKeyMap" simultaneously. '.
            'Choose one: use "morphKeyMap" for optional mapping or "enforceMorphKeyMap" for strict enforcement.',
        );
    }
}
