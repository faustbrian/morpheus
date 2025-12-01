<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Morph Type
    |--------------------------------------------------------------------------
    |
    | This option defines the default column type used when creating polymorphic
    | relationship columns via the Blueprint macros (morpheus, nullableMorpheus).
    |
    | Supported values: "ulid", "uuid", "numeric", "string"
    |
    | - "ulid": Uses ulid column type (26 chars, sortable, default)
    | - "uuid": Uses uuid column type (36 chars)
    | - "numeric": Uses unsignedBigInteger (for auto-increment PKs)
    | - "string": Uses string column (flexible, any key type)
    |
    */

    'defaultMorphType' => 'ulid',

    /*
    |--------------------------------------------------------------------------
    | Polymorphic Key Mapping
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify which column should be used as the
    | foreign key for each model in polymorphic relationships. This is
    | particularly useful when different models in your application use
    | different primary key column names, which is common in legacy systems
    | or when using ULIDs and UUIDs alongside traditional auto-incrementing
    | integer keys.
    |
    | For example, if your User model uses 'id' but your Organization model
    | uses 'ulid', you can map each model to its appropriate key column here.
    | The registry will then use the correct column when storing foreign keys.
    |
    | Note: You may only configure either 'morphKeyMap' or 'enforceMorphKeyMap',
    | not both. Choose the non-enforced variant if you want to allow models
    | without explicit mappings to use their default primary key.
    |
    */

    'morphKeyMap' => [
        // App\Models\User::class => 'id',
        // App\Models\Organization::class => 'ulid',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enforced Polymorphic Key Mapping
    |--------------------------------------------------------------------------
    |
    | This option works identically to 'morphKeyMap' above, but enables strict
    | enforcement of your key mappings. When configured, any model referenced
    | in a polymorphic relationship without an explicit mapping defined here
    | will throw a MorphKeyViolationException.
    |
    | This enforcement is useful in production environments where you want to
    | ensure all models participating in polymorphic relationships have been
    | explicitly configured, preventing potential bugs from unmapped models.
    |
    | Note: Only configure either 'morphKeyMap' or 'enforceMorphKeyMap'. Using
    | both simultaneously is not supported. Choose this enforced variant when
    | you want strict type safety for your polymorphic relationships.
    |
    */

    'enforceMorphKeyMap' => [
        // App\Models\User::class => 'id',
        // App\Models\Organization::class => 'ulid',
    ],

];
