<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Test fixture model using 'ulid' as primary key.
 *
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class OrganizationModel extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'organizations';

    protected $primaryKey = 'ulid';

    protected $keyType = 'string';
}
