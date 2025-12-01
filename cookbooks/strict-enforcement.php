<?php declare(strict_types=1);

/**
 * Cookbook: Strict Enforcement Mode
 *
 * This cookbook demonstrates how to use strict enforcement mode
 * to ensure all models used in polymorphic relationships have
 * explicit key mappings configured.
 */

use App\Models\Comment;
use App\Models\Organization;
use App\Models\Post;
use App\Models\User;
use Cline\Morpheus\Exceptions\MorphKeyViolationException;
use Cline\Morpheus\MorphKeyRegistry;

$registry = app(MorphKeyRegistry::class);

// Use enforce() to register mappings AND enable strict mode
$registry->enforce([
    User::class => 'uuid',
    Organization::class => 'ulid',
    Post::class => 'id',
]);

// These work fine - models are mapped
$registry->getKey(new User());          // Returns 'uuid'
$registry->getKey(new Organization());  // Returns 'ulid'
$registry->getKey(new Post());          // Returns 'id'

// This throws MorphKeyViolationException - Comment is not mapped!
try {
    $registry->getKey(new Comment());
} catch (MorphKeyViolationException $e) {
    // "Model [App\Models\Comment] is not mapped in the morph key registry..."
    Log::error('Unmapped model used in polymorphic relationship', [
        'model' => Comment::class,
        'error' => $e->getMessage(),
    ]);
}

// Check if enforcement is active
if ($registry->isEnforcing()) {
    // Strict mode is enabled
}

// Alternative: Enable enforcement separately from mapping
$registry->map([User::class => 'uuid']);
$registry->requireMapping();  // Now enforcement is enabled
