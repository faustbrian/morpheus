<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Morpheus\Enums\MorphType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

describe('Blueprint Macros', function (): void {
    describe('morpheus()', function (): void {
        test('creates ulid morph columns by default', function (): void {
            config(['morpheus.defaultMorphType' => 'ulid']);

            Schema::create('test_morpheus_ulid', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable');
            });

            expect(Schema::hasColumn('test_morpheus_ulid', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_morpheus_ulid', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_morpheus_ulid');
        });

        test('creates uuid morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'uuid']);

            Schema::create('test_morpheus_uuid', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable');
            });

            expect(Schema::hasColumn('test_morpheus_uuid', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_morpheus_uuid', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_morpheus_uuid');
        });

        test('creates numeric morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'numeric']);

            Schema::create('test_morpheus_numeric', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable');
            });

            expect(Schema::hasColumn('test_morpheus_numeric', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_morpheus_numeric', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_morpheus_numeric');
        });

        test('creates string morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'string']);

            Schema::create('test_morpheus_string', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable');
            });

            expect(Schema::hasColumn('test_morpheus_string', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_morpheus_string', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_morpheus_string');
        });

        test('accepts explicit MorphType override', function (): void {
            config(['morpheus.defaultMorphType' => 'ulid']);

            Schema::create('test_morpheus_override', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable', MorphType::UUID);
            });

            expect(Schema::hasColumn('test_morpheus_override', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_morpheus_override', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_morpheus_override');
        });
    });

    describe('nullableMorpheus()', function (): void {
        test('creates nullable ulid morph columns by default', function (): void {
            config(['morpheus.defaultMorphType' => 'ulid']);

            Schema::create('test_nullable_ulid', function (Blueprint $table): void {
                $table->id();
                $table->nullableMorpheus('parent');
            });

            expect(Schema::hasColumn('test_nullable_ulid', 'parent_type'))->toBeTrue();
            expect(Schema::hasColumn('test_nullable_ulid', 'parent_id'))->toBeTrue();

            Schema::dropIfExists('test_nullable_ulid');
        });

        test('creates nullable uuid morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'uuid']);

            Schema::create('test_nullable_uuid', function (Blueprint $table): void {
                $table->id();
                $table->nullableMorpheus('parent');
            });

            expect(Schema::hasColumn('test_nullable_uuid', 'parent_type'))->toBeTrue();
            expect(Schema::hasColumn('test_nullable_uuid', 'parent_id'))->toBeTrue();

            Schema::dropIfExists('test_nullable_uuid');
        });

        test('creates nullable numeric morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'numeric']);

            Schema::create('test_nullable_numeric', function (Blueprint $table): void {
                $table->id();
                $table->nullableMorpheus('parent');
            });

            expect(Schema::hasColumn('test_nullable_numeric', 'parent_type'))->toBeTrue();
            expect(Schema::hasColumn('test_nullable_numeric', 'parent_id'))->toBeTrue();

            Schema::dropIfExists('test_nullable_numeric');
        });

        test('creates nullable string morph columns when configured', function (): void {
            config(['morpheus.defaultMorphType' => 'string']);

            Schema::create('test_nullable_string', function (Blueprint $table): void {
                $table->id();
                $table->nullableMorpheus('parent');
            });

            expect(Schema::hasColumn('test_nullable_string', 'parent_type'))->toBeTrue();
            expect(Schema::hasColumn('test_nullable_string', 'parent_id'))->toBeTrue();

            Schema::dropIfExists('test_nullable_string');
        });

        test('accepts explicit MorphType override', function (): void {
            config(['morpheus.defaultMorphType' => 'ulid']);

            Schema::create('test_nullable_override', function (Blueprint $table): void {
                $table->id();
                $table->nullableMorpheus('parent', MorphType::Numeric);
            });

            expect(Schema::hasColumn('test_nullable_override', 'parent_type'))->toBeTrue();
            expect(Schema::hasColumn('test_nullable_override', 'parent_id'))->toBeTrue();

            Schema::dropIfExists('test_nullable_override');
        });
    });

    describe('default config fallback', function (): void {
        test('falls back to ULID when config is invalid', function (): void {
            config(['morpheus.defaultMorphType' => 'invalid-type']);

            Schema::create('test_invalid_fallback', function (Blueprint $table): void {
                $table->id();
                $table->morpheus('taggable');
            });

            expect(Schema::hasColumn('test_invalid_fallback', 'taggable_type'))->toBeTrue();
            expect(Schema::hasColumn('test_invalid_fallback', 'taggable_id'))->toBeTrue();

            Schema::dropIfExists('test_invalid_fallback');
        });
    });
});
