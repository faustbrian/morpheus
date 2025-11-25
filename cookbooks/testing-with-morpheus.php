<?php declare(strict_types=1);

/**
 * Cookbook: Testing with Morpheus
 *
 * This cookbook demonstrates how to properly test code that uses
 * the MorphKeyRegistry, including resetting state between tests.
 */

use Cline\Morpheus\Exceptions\MorphKeyViolationException;
use Cline\Morpheus\MorphKeyRegistry;
use Tests\Fixtures\UserModel;

// In your TestCase base class:
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function tearDown(): void
    {
        // Always reset the registry between tests to avoid state leakage
        $this->app?->make(MorphKeyRegistry::class)->reset();

        parent::tearDown();
    }
}

// Example Pest tests:
describe('YourService', function (): void {
    beforeEach(function (): void {
        // Set up mappings for your tests
        $this->registry = app(MorphKeyRegistry::class);
        $this->registry->map([
            UserModel::class => 'uuid',
        ]);
    });

    test('stores polymorphic relation with correct key', function (): void {
        $user = new UserModel();
        $user->uuid = 'test-uuid-123';

        $service = app(YourService::class);
        $service->storePolymorphicRelation($user);

        expect(DB::table('your_table')->where('related_id', 'test-uuid-123')->exists())
            ->toBeTrue();
    });

    test('throws exception for unmapped model in strict mode', function (): void {
        $this->registry->enforce([UserModel::class => 'uuid']);

        $unmappedModel = new UnmappedModel();

        expect(fn () => $this->registry->getKey($unmappedModel))
            ->toThrow(MorphKeyViolationException::class);
    });

    test('uses default key when not mapped and not enforcing', function (): void {
        // Registry has no mapping for this model
        $model = new ModelWithCustomKey();  // Has $primaryKey = 'custom_id'

        expect($this->registry->getKey($model))->toBe('custom_id');
    });
});

// Testing configuration integration:
describe('Configuration', function (): void {
    test('applies morphKeyMap from config', function (): void {
        config(['your-package.morphKeyMap' => [
            UserModel::class => 'custom_key',
        ]]);

        // Re-register provider to apply config
        $this->app->register(YourPackageServiceProvider::class, force: true);

        $registry = app(MorphKeyRegistry::class);

        expect($registry->getKeyFromClass(UserModel::class))->toBe('custom_key');
    });
});
