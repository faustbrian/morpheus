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
 * Exception thrown when a model lacks required morph key mapping.
 *
 * Occurs when enforcement mode is enabled and a model is used in a polymorphic
 * relationship without a defined key mapping in the MorphKeyRegistry. This ensures
 * all polymorphic relationships have explicit, type-safe key configurations.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MorphKeyViolationException extends RuntimeException
{
    /**
     * Creates exception for a model without key mapping in enforcement mode.
     *
     * Provides detailed error message identifying the unmapped model class
     * and instructions for resolving the violation by registering the model
     * in the MorphKeyRegistry.
     *
     * @param  string $class Fully qualified class name or morph alias of the unmapped model that triggered the violation
     * @return self   Exception instance with detailed violation message
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
