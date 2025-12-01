<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Enums;

/**
 * Defines polymorphic column types for Laravel migrations.
 *
 * Maps to Laravel Blueprint's morph method variants, enabling type-safe
 * polymorphic relationships that match your model's primary key type.
 * Used with Blueprint::morpheus() and Blueprint::nullableMorpheus() macros.
 *
 * ```php
 * // In a migration:
 * $table->morpheus('commentable', MorphType::UUID);
 * // Creates: commentable_id (uuid) and commentable_type (string)
 *
 * $table->nullableMorpheus('taggable', MorphType::ULID);
 * // Creates: nullable taggable_id (ulid) and taggable_type (string)
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 */
enum MorphType: string
{
    /**
     * Standard polymorphic columns using unsignedBigInteger for the ID.
     *
     * Creates polymorphic columns with unsignedBigInteger type for the ID field.
     * Best suited for models using auto-incrementing integer primary keys.
     * Maps to Blueprint::numericMorphs() and Blueprint::nullableNumericMorphs().
     */
    case Numeric = 'numeric';

    /**
     * UUID-based polymorphic columns using uuid type for the ID.
     *
     * Creates polymorphic columns with uuid type for the ID field. Ideal for
     * models using UUID primary keys, providing globally unique identifiers
     * across distributed systems. Maps to Blueprint::uuidMorphs() and
     * Blueprint::nullableUuidMorphs().
     */
    case UUID = 'uuid';

    /**
     * ULID-based polymorphic columns using ulid type for the ID.
     *
     * Creates polymorphic columns with ulid type for the ID field. Best for
     * models using ULID primary keys, combining time-ordering with uniqueness.
     * Maps to Blueprint::ulidMorphs() and Blueprint::nullableUlidMorphs().
     */
    case ULID = 'ulid';

    /**
     * String-based polymorphic columns using string type for the ID.
     *
     * Creates polymorphic columns with string type for the ID field. Most flexible
     * option that works with any key type but sacrifices database-level type safety
     * and potential performance optimizations. Maps to Blueprint::morphs() and
     * Blueprint::nullableMorphs().
     */
    case String = 'string';
}
