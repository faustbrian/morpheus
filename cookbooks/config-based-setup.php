<?php declare(strict_types=1);

/**
 * Cookbook: Configuration-Based Setup
 *
 * This cookbook demonstrates how to configure morph key mappings
 * via Laravel's configuration system instead of programmatically.
 */

// In config/morpheus.php (publish with: php artisan vendor:publish --tag=morpheus-config)
return [
    // Option 1: Non-enforced mappings (unmapped models use their default key)
    'morphKeyMap' => [
        App\Models\User::class => 'uuid',
        App\Models\Organization::class => 'ulid',
        App\Models\Team::class => 'id',
    ],

    // Option 2: Enforced mappings (unmapped models throw exception)
    // Note: Only use ONE of these options, not both!
    'enforceMorphKeyMap' => [
        // App\Models\User::class => 'uuid',
        // App\Models\Organization::class => 'ulid',
    ],
];

// The MorpheusServiceProvider automatically reads this config on boot.
// No additional code needed - just publish and configure!
