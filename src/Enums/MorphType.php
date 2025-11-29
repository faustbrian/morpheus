<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Enums;

/**
 * Defines the type of morph columns to create in migrations.
 *
 * Maps to Laravel's Blueprint morph method variants for polymorphic relationships.
 *
 * @author Brian Faust <brian@cline.sh>
 */
enum MorphType: string
{
    /**
     * Standard morphs - uses unsignedBigInteger for the ID column.
     * Best for auto-incrementing integer primary keys.
     */
    case Numeric = 'numeric';

    /**
     * UUID morphs - uses uuid for the ID column.
     * Best for UUID primary keys.
     */
    case UUID = 'uuid';

    /**
     * ULID morphs - uses ulid for the ID column.
     * Best for ULID primary keys.
     */
    case ULID = 'ulid';

    /**
     * Generic morphs - uses string for the ID column.
     * Flexible but less type-safe, works with any key type.
     */
    case String = 'string';
}
