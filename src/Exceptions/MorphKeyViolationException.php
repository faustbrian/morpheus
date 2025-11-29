<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Exceptions;

use RuntimeException;

use function sprintf;

/**
 * Exception thrown when a model is used in a polymorphic relationship
 * without a defined key mapping and enforcement is enabled.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MorphKeyViolationException extends RuntimeException
{
    /**
     * Create an exception for an unmapped model class.
     *
     * @param class-string $class The model class that has no key mapping
     */
    public static function forClass(string $class): self
    {
        return new self(sprintf(
            'Model [%s] is not mapped in the morph key registry. '.
            'Use MorphKeyRegistry::map() or MorphKeyRegistry::enforce() to define key mappings.',
            $class,
        ));
    }
}
