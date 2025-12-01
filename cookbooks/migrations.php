<?php declare(strict_types=1);

/**
 * Cookbook: Using Morpheus in Migrations
 *
 * This cookbook demonstrates how to use Blueprint macros
 * for creating polymorphic columns in database migrations.
 */

use Cline\Morpheus\Enums\MorphType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Example migration using morpheus() macro.
 *
 * The macro automatically uses the configured default morph type
 * from config/morpheus.php (defaults to ULID).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->text('body');

            // Creates commentable_type (string) and commentable_id (ulid by default)
            $table->morpheus('commentable');

            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');

            // Creates taggable_type and taggable_id columns
            // Uses default morph type from config
            $table->morpheus('taggable');

            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('event');

            // Creates subject_type and subject_id (required)
            $table->morpheus('subject');

            // Creates causer_type and causer_id (nullable)
            $table->nullableMorpheus('causer');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('comments');
    }
};

/**
 * Example: Overriding the default morph type per-column.
 *
 * You can pass a MorphType enum value to override the default
 * for specific columns when needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_relations', function (Blueprint $table): void {
            $table->id();

            // Force numeric morphs for legacy integer PKs
            $table->morpheus('legacy_model', MorphType::Numeric);

            // Force UUID morphs for external system
            $table->nullableMorpheus('external_ref', MorphType::UUID);

            // Use default (ULID) for new models
            $table->morpheus('new_model');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_relations');
    }
};

/**
 * Example: Available MorphType options.
 */

// MorphType::ULID - Creates ulid column (26 chars, sortable, default)
// $table->morpheus('subject', MorphType::ULID);

// MorphType::UUID - Creates uuid column (36 chars)
// $table->morpheus('subject', MorphType::UUID);

// MorphType::Numeric - Creates unsignedBigInteger (for auto-increment PKs)
// $table->morpheus('subject', MorphType::Numeric);

// MorphType::String - Creates string column (flexible, any key type)
// $table->morpheus('subject', MorphType::String);

/**
 * Before Morpheus (verbose):
 */
// match ($boundaryMorphType) {
//     MorphType::ULID => $table->nullableUlidMorphs('boundary'),
//     MorphType::UUID => $table->nullableUuidMorphs('boundary'),
//     MorphType::Numeric => $table->nullableNumericMorphs('boundary'),
//     MorphType::String => $table->nullableMorphs('boundary'),
// };

/**
 * After Morpheus (clean):
 */
// $table->nullableMorpheus('boundary');
// or with explicit type:
// $table->nullableMorpheus('boundary', MorphType::UUID);
