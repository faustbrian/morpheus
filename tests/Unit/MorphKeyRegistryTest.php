<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Morpheus\Exceptions\MorphKeyViolationException;
use Cline\Morpheus\MorphKeyRegistry;
use Tests\Fixtures\OrganizationModel;
use Tests\Fixtures\PostModel;
use Tests\Fixtures\UserModel;

describe('MorphKeyRegistry', function (): void {
    beforeEach(function (): void {
        $this->registry = new MorphKeyRegistry();
    });

    describe('map()', function (): void {
        test('registers key mappings', function (): void {
            $this->registry->map([
                UserModel::class => 'id',
                OrganizationModel::class => 'ulid',
            ]);

            expect($this->registry->has(UserModel::class))->toBeTrue();
            expect($this->registry->has(OrganizationModel::class))->toBeTrue();
        });

        test('merges additional mappings', function (): void {
            $this->registry->map([UserModel::class => 'id']);
            $this->registry->map([OrganizationModel::class => 'ulid']);

            expect($this->registry->all())->toBe([
                UserModel::class => 'id',
                OrganizationModel::class => 'ulid',
            ]);
        });

        test('overwrites existing mappings', function (): void {
            $this->registry->map([UserModel::class => 'id']);
            $this->registry->map([UserModel::class => 'uuid']);

            expect($this->registry->getKeyFromClass(UserModel::class))->toBe('uuid');
        });
    });

    describe('enforce()', function (): void {
        test('registers mappings and enables enforcement', function (): void {
            $this->registry->enforce([UserModel::class => 'id']);

            expect($this->registry->has(UserModel::class))->toBeTrue();
            expect($this->registry->isEnforcing())->toBeTrue();
        });
    });

    describe('requireMapping()', function (): void {
        test('enables enforcement without adding mappings', function (): void {
            $this->registry->map([UserModel::class => 'id']);
            $this->registry->requireMapping();

            expect($this->registry->isEnforcing())->toBeTrue();
            expect($this->registry->all())->toBe([UserModel::class => 'id']);
        });

        test('can be called before map()', function (): void {
            $this->registry->requireMapping();
            $this->registry->map([UserModel::class => 'id']);

            expect($this->registry->isEnforcing())->toBeTrue();
            expect($this->registry->getKeyFromClass(UserModel::class))->toBe('id');
        });
    });

    describe('isEnforcing()', function (): void {
        test('returns false by default', function (): void {
            expect($this->registry->isEnforcing())->toBeFalse();
        });

        test('returns true after requireMapping()', function (): void {
            $this->registry->requireMapping();

            expect($this->registry->isEnforcing())->toBeTrue();
        });

        test('returns true after enforce()', function (): void {
            $this->registry->enforce([]);

            expect($this->registry->isEnforcing())->toBeTrue();
        });
    });

    describe('getKey()', function (): void {
        test('returns mapped key for registered model', function (): void {
            $this->registry->map([UserModel::class => 'uuid']);

            $model = new UserModel();

            expect($this->registry->getKey($model))->toBe('uuid');
        });

        test('returns model default key when not mapped and not enforcing', function (): void {
            $model = new OrganizationModel();

            expect($this->registry->getKey($model))->toBe('ulid');
        });

        test('throws exception when not mapped and enforcing', function (): void {
            $this->registry->enforce([UserModel::class => 'id']);

            $model = new OrganizationModel();

            expect(fn () => $this->registry->getKey($model))
                ->toThrow(MorphKeyViolationException::class);
        });
    });

    describe('getValue()', function (): void {
        test('returns value from mapped key column', function (): void {
            $this->registry->map([UserModel::class => 'uuid']);

            $model = new UserModel();
            $model->uuid = 'test-uuid-value';

            expect($this->registry->getValue($model))->toBe('test-uuid-value');
        });

        test('returns value from default key when not mapped', function (): void {
            $model = new UserModel();
            $model->id = 123;

            expect($this->registry->getValue($model))->toBe(123);
        });
    });

    describe('getKeyFromClass()', function (): void {
        test('returns mapped key for registered class', function (): void {
            $this->registry->map([UserModel::class => 'uuid']);

            expect($this->registry->getKeyFromClass(UserModel::class))->toBe('uuid');
        });

        test('returns model default key when not mapped and not enforcing', function (): void {
            expect($this->registry->getKeyFromClass(OrganizationModel::class))->toBe('ulid');
        });

        test('throws exception when not mapped and enforcing', function (): void {
            $this->registry->enforce([UserModel::class => 'id']);

            expect(fn () => $this->registry->getKeyFromClass(PostModel::class))
                ->toThrow(MorphKeyViolationException::class);
        });
    });

    describe('has()', function (): void {
        test('returns true for registered model instance', function (): void {
            $this->registry->map([UserModel::class => 'id']);

            expect($this->registry->has(
                new UserModel(),
            ))->toBeTrue();
        });

        test('returns true for registered class string', function (): void {
            $this->registry->map([UserModel::class => 'id']);

            expect($this->registry->has(UserModel::class))->toBeTrue();
        });

        test('returns false for unregistered model', function (): void {
            expect($this->registry->has(UserModel::class))->toBeFalse();
        });
    });

    describe('all()', function (): void {
        test('returns all registered mappings', function (): void {
            $this->registry->map([
                UserModel::class => 'id',
                OrganizationModel::class => 'ulid',
            ]);

            expect($this->registry->all())->toBe([
                UserModel::class => 'id',
                OrganizationModel::class => 'ulid',
            ]);
        });

        test('returns empty array when no mappings', function (): void {
            expect($this->registry->all())->toBe([]);
        });
    });

    describe('reset()', function (): void {
        test('clears all mappings', function (): void {
            $this->registry->map([UserModel::class => 'id']);
            $this->registry->reset();

            expect($this->registry->all())->toBe([]);
        });

        test('disables enforcement', function (): void {
            $this->registry->enforce([UserModel::class => 'id']);
            $this->registry->reset();

            expect($this->registry->isEnforcing())->toBeFalse();
        });
    });
});
