<?php declare(strict_types=1);

/**
 * Cookbook: Integrating into Your Own Package
 *
 * This cookbook demonstrates how to integrate morpheus into your
 * own Laravel package using the ConfiguresMorpheus trait.
 */

namespace YourPackage;

use Cline\Morpheus\Concerns\ConfiguresMorpheus;
use Cline\Morpheus\MorphKeyRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Example: Your package's service provider
 */
final class YourPackageServiceProvider extends ServiceProvider
{
    // Include the trait for config-based morph key setup
    use ConfiguresMorpheus;

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/your-package.php', 'your-package');
    }

    public function boot(): void
    {
        // This reads 'your-package.morphKeyMap' and 'your-package.enforceMorphKeyMap'
        // from your package's config file and applies them to the shared registry
        $this->configureMorpheus('your-package');
    }
}

/**
 * Example: Using the registry in your package's service class
 */
final class YourService
{
    public function __construct(
        private readonly MorphKeyRegistry $registry,
    ) {}

    public function storePolymorphicRelation(Model $model): void
    {
        // Get the correct key column for this model type
        $keyColumn = $this->registry->getKey($model);
        $keyValue = $this->registry->getValue($model);

        // Store in your pivot table
        DB::table('your_polymorphic_table')->insert([
            'related_type' => $model->getMorphClass(),
            'related_id' => $keyValue,  // Uses the correct key (uuid, ulid, or id)
            'created_at' => now(),
        ]);
    }

    public function resolvePolymorphicRelation(string $type, string|int $id): ?Model
    {
        // Get the key column for this model type
        $keyColumn = $this->registry->getKeyFromClass($type);

        // Query using the correct key column
        return $type::where($keyColumn, $id)->first();
    }
}

/**
 * Example: Your package's config file (config/your-package.php)
 */
return [
    // ... other config options ...

    'morphKeyMap' => [
        // Users can configure their model key mappings here
        // App\Models\User::class => 'uuid',
    ],

    'enforceMorphKeyMap' => [
        // Or use enforced mappings for strict mode
        // App\Models\User::class => 'uuid',
    ],
];
