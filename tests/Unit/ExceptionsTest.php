<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Morpheus\Exceptions\InvalidConfigurationException;
use Cline\Morpheus\Exceptions\MorphKeyViolationException;
use Tests\Fixtures\UserModel;

describe('MorphKeyViolationException', function (): void {
    test('forClass() creates exception with descriptive message', function (): void {
        $exception = MorphKeyViolationException::forClass(UserModel::class);

        expect($exception)->toBeInstanceOf(MorphKeyViolationException::class);
        expect($exception->getMessage())->toContain(UserModel::class);
        expect($exception->getMessage())->toContain('MorphKeyRegistry::map()');
        expect($exception->getMessage())->toContain('MorphKeyRegistry::enforce()');
    });

    test('extends RuntimeException', function (): void {
        $exception = MorphKeyViolationException::forClass(UserModel::class);

        expect($exception)->toBeInstanceOf(RuntimeException::class);
    });
});

describe('InvalidConfigurationException', function (): void {
    test('conflictingMorphKeyMaps() creates exception with descriptive message', function (): void {
        $exception = InvalidConfigurationException::conflictingMorphKeyMaps();

        expect($exception)->toBeInstanceOf(InvalidConfigurationException::class);
        expect($exception->getMessage())->toContain('morphKeyMap');
        expect($exception->getMessage())->toContain('enforceMorphKeyMap');
        expect($exception->getMessage())->toContain('Cannot configure both');
    });

    test('extends InvalidArgumentException', function (): void {
        $exception = InvalidConfigurationException::conflictingMorphKeyMaps();

        expect($exception)->toBeInstanceOf(InvalidArgumentException::class);
    });
});
