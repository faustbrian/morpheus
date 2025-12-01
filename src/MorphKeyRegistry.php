<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Morpheus;

use Cline\Morpheus\Exceptions\MorphKeyViolationException;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

use function array_key_exists;
use function array_merge;
use function class_exists;

/**
 * Registry for managing polymorphic relationship key mappings across model types.
 *
 * Provides centralized configuration for which primary key column each model uses
 * in polymorphic relationships. Essential for applications where different models
 * use different key types (e.g., User with 'uuid', Seller with 'ulid', Post with 'id').
 *
 * Supports two modes: optional mapping (allows fallback to model defaults) and
 * enforcement mode (requires explicit mappings for all models). Registered as
 * singleton to ensure consistent key resolution throughout the application.
 *
 * ```php
 * // Optional mapping with fallback
 * $registry->map([
 *     User::class => 'uuid',
 *     Seller::class => 'ulid',
 * ]);
 *
 * // Strict enforcement - all models must be mapped
 * $registry->enforce([
 *     User::class => 'uuid',
 *     Seller::class => 'ulid',
 * ]);
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 */
#[Singleton()]
final class MorphKeyRegistry
{
    /**
     * Polymorphic key column name mappings indexed by model class.
     *
     * Maps fully qualified model class names to their corresponding primary key
     * column names. Enables different models to use different key types (uuid, ulid, id)
     * within the same polymorphic relationship structure.
     *
     * ```php
     * [
     *     User::class => 'uuid',
     *     Seller::class => 'ulid',
     *     Organization::class => 'id',
     * ]
     * ```
     *
     * @var array<class-string, string>
     */
    private array $keyMap = [];

    /**
     * Enforcement mode flag for strict key mapping validation.
     *
     * When true, requires all models used in polymorphic relationships to have
     * explicit key mappings. Using an unmapped model throws MorphKeyViolationException.
     * When false, unmapped models fall back to their getKeyName() method.
     */
    private bool $enforceKeyMap = false;

    /**
     * Registers polymorphic key mappings for model classes.
     *
     * Defines which primary key column name each model class uses in polymorphic
     * relationships. Allows mixing different key types (uuid, ulid, id) across
     * models within the same application. Mappings are merged with existing ones,
     * allowing incremental registration from multiple sources.
     *
     * ```php
     * $registry->map([
     *     User::class => 'uuid',
     *     Seller::class => 'ulid',
     *     Post::class => 'id',
     * ]);
     * ```
     *
     * @param array<class-string, string> $map Associative array mapping model class names to primary key column names
     */
    public function map(array $map): void
    {
        $this->keyMap = array_merge($this->keyMap, $map);
    }

    /**
     * Registers polymorphic key mappings and enables strict enforcement mode.
     *
     * Combines map() functionality with automatic enforcement activation. After calling
     * this method, all models used in polymorphic relationships must have explicit key
     * mappings or a MorphKeyViolationException will be thrown. Prevents accidental use
     * of unmapped models and ensures type-safe polymorphic relationships.
     *
     * ```php
     * $registry->enforce([
     *     User::class => 'uuid',
     *     Seller::class => 'ulid',
     * ]);
     *
     * // These succeed - models are mapped
     * $registry->getKey($user);     // Returns 'uuid'
     * $registry->getKey($seller);   // Returns 'ulid'
     *
     * // This throws MorphKeyViolationException - Post not mapped
     * $registry->getKey($post);
     * ```
     *
     * @param array<class-string, string> $map Associative array mapping model class names to primary key column names
     */
    public function enforce(array $map): void
    {
        $this->map($map);
        $this->requireMapping();
    }

    /**
     * Enables strict enforcement of key mappings for all models.
     *
     * Activates enforcement mode where all models used in polymorphic relationships
     * must have defined key mappings. Unmapped models will trigger MorphKeyViolationException
     * when getKey() or getValue() is called. Typically invoked through enforce() rather
     * than directly, unless you need to enable enforcement without adding new mappings.
     */
    public function requireMapping(): void
    {
        $this->enforceKeyMap = true;
    }

    /**
     * Checks if a model class has a registered key mapping.
     *
     * Determines whether the given model class or instance has an explicit
     * key mapping defined in the registry. Useful for conditional logic when
     * you need to know if a model is mapped before attempting to retrieve its key.
     *
     * @param  class-string|Model $model Model instance or fully qualified class name to check
     * @return bool               True if the model has a registered key mapping, false otherwise
     */
    public function has(Model|string $model): bool
    {
        $class = $model instanceof Model ? $model::class : $model;

        return array_key_exists($class, $this->keyMap);
    }

    /**
     * Retrieves the polymorphic key column name for a model.
     *
     * Returns the configured key mapping if one exists, otherwise falls back to
     * the model's getKeyName() method when enforcement is disabled. In enforcement
     * mode, unmapped models trigger a MorphKeyViolationException to ensure explicit
     * configuration for all polymorphic relationships.
     *
     * ```php
     * $key = $registry->getKey($user);
     * // Returns 'uuid' if User is mapped to 'uuid'
     * // Returns $user->getKeyName() if not mapped and enforcement is disabled
     * // Throws MorphKeyViolationException if not mapped and enforcement is enabled
     * ```
     *
     * @param Model $model Model instance to retrieve the key column name for
     *
     * @throws MorphKeyViolationException When enforcement mode is enabled and the model has no registered mapping
     *
     * @return string Primary key column name (e.g., 'id', 'uuid', 'ulid')
     */
    public function getKey(Model $model): string
    {
        $class = $model::class;

        if (array_key_exists($class, $this->keyMap)) {
            return $this->keyMap[$class];
        }

        if ($this->enforceKeyMap) {
            throw MorphKeyViolationException::forClass($class);
        }

        return $model->getKeyName();
    }

    /**
     * Retrieves the polymorphic key value for a model instance.
     *
     * Returns the actual value of the model's configured primary key column.
     * Uses getKey() to determine the correct column name, then retrieves the
     * value from the model's attributes. Subject to the same enforcement rules
     * as getKey().
     *
     * @param Model $model Model instance to retrieve the key value from
     *
     * @throws MorphKeyViolationException When enforcement mode is enabled and the model has no registered mapping
     *
     * @return int|string The primary key value from the model
     */
    public function getValue(Model $model): int|string
    {
        $keyName = $this->getKey($model);

        /** @var int|string */
        return $model->getAttribute($keyName);
    }

    /**
     * Retrieves the polymorphic key column name from a class string.
     *
     * Similar to getKey() but accepts a fully qualified class name or morph alias
     * instead of a model instance. Automatically resolves morph aliases (e.g., 'Account')
     * to their fully qualified class names using Laravel's morph map. Useful for
     * determining key types without instantiating the model, such as in static contexts
     * or when building queries. Falls back to instantiating the model and calling
     * getKeyName() when no mapping exists and enforcement is disabled.
     *
     * @param string $class Fully qualified model class name or morph alias
     *
     * @throws MorphKeyViolationException When enforcement mode is enabled and the class has no registered mapping
     *
     * @return string Primary key column name for the model class
     */
    public function getKeyFromClass(string $class): string
    {
        // Resolve morph alias to fully qualified class name if needed
        $resolvedClass = $this->resolveMorphAlias($class);

        if (array_key_exists($resolvedClass, $this->keyMap)) {
            return $this->keyMap[$resolvedClass];
        }

        if ($this->enforceKeyMap) {
            throw MorphKeyViolationException::forClass($resolvedClass);
        }

        /** @phpstan-ignore-next-line Instantiated class is expected to be Model with getKeyName() */
        return new $resolvedClass()->getKeyName();
    }

    /**
     * Resolves a morph alias to its fully qualified class name.
     *
     * Uses Laravel's Relation::getMorphedModel() to resolve short morph aliases
     * (e.g., 'Account') to their fully qualified class names (e.g., 'App\Models\Account').
     * If the input is already a fully qualified class name or no morph map entry exists,
     * returns the original input unchanged.
     *
     * @param  string $class Class name or morph alias to resolve
     * @return string Fully qualified class name
     */
    public function resolveMorphAlias(string $class): string
    {
        // If it's already a valid class, return as-is
        if (class_exists($class)) {
            return $class;
        }

        // Try to resolve via Laravel's morph map
        $resolved = Relation::getMorphedModel($class);

        return $resolved ?? $class;
    }

    /**
     * Checks if enforcement mode is currently enabled.
     *
     * Returns whether the registry is operating in strict enforcement mode,
     * where all models must have explicit key mappings. Useful for conditional
     * logic or debugging to understand the current registry configuration state.
     *
     * @return bool True if enforcement mode is enabled, false otherwise
     */
    public function isEnforcing(): bool
    {
        return $this->enforceKeyMap;
    }

    /**
     * Retrieves all registered key mappings.
     *
     * Returns the complete mapping array showing which primary key column each
     * model class is configured to use. Useful for debugging, testing, or when
     * you need to introspect the current registry state.
     *
     * @return array<class-string, string> Associative array of model class names to key column names
     */
    public function all(): array
    {
        return $this->keyMap;
    }

    /**
     * Resets the registry to its initial state.
     *
     * Clears all registered key mappings and disables enforcement mode, restoring
     * the registry to a clean state. Primarily useful in testing environments to
     * ensure test isolation and prevent state leakage between test cases.
     */
    public function reset(): void
    {
        $this->keyMap = [];
        $this->enforceKeyMap = false;
    }
}
