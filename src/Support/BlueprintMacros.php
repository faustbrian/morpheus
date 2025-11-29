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
 * Blueprint macros for creating polymorphic columns with the configured morph type.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class BlueprintMacros
{
    /**
     * Register all Morpheus Blueprint macros.
     */
    public static function register(): void
    {
        self::registerMorpheus();
        self::registerNullableMorpheus();
    }

    /**
     * Get the default morph type from configuration.
     */
    public static function getDefaultMorphType(): MorphType
    {
        /** @var string $configValue */
        $configValue = config('morpheus.defaultMorphType', MorphType::ULID->value);

        return MorphType::tryFrom($configValue) ?? MorphType::ULID;
    }

    /**
     * Register the morpheus() macro for required polymorphic columns.
     */
    private static function registerMorpheus(): void
    {
        Blueprint::macro('morpheus', function (string $name, ?MorphType $type = null): void {
            /** @var Blueprint $this */
            $morphType = $type ?? BlueprintMacros::getDefaultMorphType();

            match ($morphType) {
                MorphType::ULID => $this->ulidMorphs($name),
                MorphType::UUID => $this->uuidMorphs($name),
                MorphType::Numeric => $this->numericMorphs($name),
                MorphType::String => $this->morphs($name),
            };
        });
    }

    /**
     * Register the nullableMorpheus() macro for optional polymorphic columns.
     */
    private static function registerNullableMorpheus(): void
    {
        Blueprint::macro('nullableMorpheus', function (string $name, ?MorphType $type = null): void {
            /** @var Blueprint $this */
            $morphType = $type ?? BlueprintMacros::getDefaultMorphType();

            match ($morphType) {
                MorphType::ULID => $this->nullableUlidMorphs($name),
                MorphType::UUID => $this->nullableUuidMorphs($name),
                MorphType::Numeric => $this->nullableNumericMorphs($name),
                MorphType::String => $this->nullableMorphs($name),
            };
        });
    }
}
