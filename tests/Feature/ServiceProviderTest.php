<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Morpheus\Exceptions\InvalidConfigurationException;
use Cline\Morpheus\MorpheusServiceProvider;
use Cline\Morpheus\MorphKeyRegistry;
use Tests\Fixtures\OrganizationModel;
use Tests\Fixtures\UserModel;

describe('MorpheusServiceProvider', function (): void {
    describe('service container', function (): void {
        test('registers MorphKeyRegistry as singleton', function (): void {
            $registry1 = $this->app->make(MorphKeyRegistry::class);
            $registry2 = $this->app->make(MorphKeyRegistry::class);

            expect($registry1)->toBe($registry2);
        });
    });

    describe('config loading', function (): void {
        test('loads default config', function (): void {
            expect(config('morpheus.morphKeyMap'))->toBeArray();
            expect(config('morpheus.enforceMorphKeyMap'))->toBeArray();
        });
    });

    describe('morphKeyMap configuration', function (): void {
        test('applies morphKeyMap from config', function (): void {
            config(['morpheus.morphKeyMap' => [
                UserModel::class => 'uuid',
            ]]);

            // Reboot the provider to apply config
            $this->app->make(MorphKeyRegistry::class)->reset();
            $this->app->register(MorpheusServiceProvider::class, force: true);
            $this->app->boot();

            /** @var MorphKeyRegistry $registry */
            $registry = $this->app->make(MorphKeyRegistry::class);

            expect($registry->has(UserModel::class))->toBeTrue();
            expect($registry->getKeyFromClass(UserModel::class))->toBe('uuid');
            expect($registry->isEnforcing())->toBeFalse();
        });
    });

    describe('enforceMorphKeyMap configuration', function (): void {
        test('applies enforceMorphKeyMap from config', function (): void {
            config([
                'morpheus.morphKeyMap' => [],
                'morpheus.enforceMorphKeyMap' => [
                    UserModel::class => 'uuid',
                ],
            ]);

            // Reboot the provider to apply config
            $this->app->make(MorphKeyRegistry::class)->reset();
            $this->app->register(MorpheusServiceProvider::class, force: true);
            $this->app->boot();

            /** @var MorphKeyRegistry $registry */
            $registry = $this->app->make(MorphKeyRegistry::class);

            expect($registry->has(UserModel::class))->toBeTrue();
            expect($registry->isEnforcing())->toBeTrue();
        });
    });

    describe('conflicting configuration', function (): void {
        test('throws exception when both morphKeyMap and enforceMorphKeyMap are configured', function (): void {
            config([
                'morpheus.morphKeyMap' => [UserModel::class => 'id'],
                'morpheus.enforceMorphKeyMap' => [OrganizationModel::class => 'ulid'],
            ]);

            // Reboot the provider to apply config
            $this->app->make(MorphKeyRegistry::class)->reset();

            expect(function (): void {
                $this->app->register(MorpheusServiceProvider::class, force: true);
                $this->app->boot();
            })->toThrow(InvalidConfigurationException::class);
        });
    });

    describe('invalid configuration values', function (): void {
        test('handles non-array morphKeyMap gracefully', function (): void {
            config([
                'morpheus.morphKeyMap' => 'invalid-string-value',
                'morpheus.enforceMorphKeyMap' => [],
            ]);

            // Reboot the provider to apply config
            $this->app->make(MorphKeyRegistry::class)->reset();
            $this->app->register(MorpheusServiceProvider::class, force: true);
            $this->app->boot();

            /** @var MorphKeyRegistry $registry */
            $registry = $this->app->make(MorphKeyRegistry::class);

            // Should have no mappings since invalid value was ignored
            expect($registry->all())->toBe([]);
        });

        test('handles non-array enforceMorphKeyMap gracefully', function (): void {
            config([
                'morpheus.morphKeyMap' => [],
                'morpheus.enforceMorphKeyMap' => 'invalid-string-value',
            ]);

            // Reboot the provider to apply config
            $this->app->make(MorphKeyRegistry::class)->reset();
            $this->app->register(MorpheusServiceProvider::class, force: true);
            $this->app->boot();

            /** @var MorphKeyRegistry $registry */
            $registry = $this->app->make(MorphKeyRegistry::class);

            // Should have no mappings and not be enforcing
            expect($registry->all())->toBe([]);
            expect($registry->isEnforcing())->toBeFalse();
        });
    });
});
