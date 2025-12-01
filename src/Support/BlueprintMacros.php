<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus\Support;

use Cline\Morpheus\Enums\MorphType;
use Illuminate\Database\Schema\Blueprint;

use function config;

/**
 * Blueprint macro registration for type-safe polymorphic column creation.
 *
 * Provides morpheus() and nullableMorpheus() macros that create polymorphic
 * relationship columns using the configured default morph type (or an explicitly
 * provided type). Simplifies migration code by centralizing type selection logic.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class BlueprintMacros
{
    /**
     * Registers all Morpheus Blueprint macros with Laravel.
     *
     * Adds morpheus() and nullableMorpheus() methods to the Blueprint class,
     * making them available in migration files for creating type-aware
     * polymorphic relationship columns.
     */
    public static function register(): void
    {
        self::registerMorpheus();
        self::registerNullableMorpheus();
    }

    /**
     * Retrieves the default morph type from application configuration.
     *
     * Reads the morpheus.defaultMorphType config value and returns the
     * corresponding MorphType enum. Falls back to ULID if the configured
     * value is invalid or not set.
     *
     * @return MorphType Default morph type to use for polymorphic columns
     */
    public static function getDefaultMorphType(): MorphType
    {
        /** @var string $configValue */
        $configValue = config('morpheus.defaultMorphType', MorphType::ULID->value);

        return MorphType::tryFrom($configValue) ?? MorphType::ULID;
    }

    /**
     * Registers the morpheus() Blueprint macro for required polymorphic columns.
     *
     * Adds a morpheus() method to Blueprint that creates polymorphic relationship
     * columns (both _id and _type) with the appropriate ID column type based on
     * the provided MorphType or the configured default. The created columns are
     * non-nullable.
     *
     * ```php
     * // In a migration:
     * $table->morpheus('commentable', MorphType::UUID);
     * // Creates: commentable_id (uuid, non-null) and commentable_type (string, non-null)
     *
     * $table->morpheus('taggable'); // Uses default from config
     * ```
     */
    private static function registerMorpheus(): void
    {
        Blueprint::macro('morpheus', function (string $name, ?MorphType $type = null): void {
            /** @var Blueprint $this */
            $morphType = $type ?? BlueprintMacros::getDefaultMorphType();

            // Delegate to appropriate Laravel Blueprint method based on morph type
            match ($morphType) {
                MorphType::ULID => $this->ulidMorphs($name),
                MorphType::UUID => $this->uuidMorphs($name),
                MorphType::Numeric => $this->numericMorphs($name),
                MorphType::String => $this->morphs($name),
            };
        });
    }

    /**
     * Registers the nullableMorpheus() Blueprint macro for optional polymorphic columns.
     *
     * Adds a nullableMorpheus() method to Blueprint that creates nullable polymorphic
     * relationship columns (both _id and _type) with the appropriate ID column type
     * based on the provided MorphType or the configured default. Both columns are
     * nullable, allowing the relationship to be optional.
     *
     * ```php
     * // In a migration:
     * $table->nullableMorpheus('commentable', MorphType::UUID);
     * // Creates: commentable_id (uuid, nullable) and commentable_type (string, nullable)
     *
     * $table->nullableMorpheus('taggable'); // Uses default from config
     * ```
     */
    private static function registerNullableMorpheus(): void
    {
        Blueprint::macro('nullableMorpheus', function (string $name, ?MorphType $type = null): void {
            /** @var Blueprint $this */
            $morphType = $type ?? BlueprintMacros::getDefaultMorphType();

            // Delegate to appropriate Laravel Blueprint method based on morph type
            match ($morphType) {
                MorphType::ULID => $this->nullableUlidMorphs($name),
                MorphType::UUID => $this->nullableUuidMorphs($name),
                MorphType::Numeric => $this->nullableNumericMorphs($name),
                MorphType::String => $this->nullableMorphs($name),
            };
        });
    }
}
