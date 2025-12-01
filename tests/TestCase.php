<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Cline\Morpheus\MorpheusServiceProvider;
use Cline\Morpheus\MorphKeyRegistry;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Override;

/**
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Reset the registry between tests.
     */
    #[Override()]
    protected function tearDown(): void
    {
        $this->app?->make(MorphKeyRegistry::class)->reset();

        parent::tearDown();
    }

    /**
     * @param  Application              $app
     * @return array<int, class-string>
     */
    #[Override()]
    protected function getPackageProviders($app): array
    {
        return [
            MorpheusServiceProvider::class,
        ];
    }
}
