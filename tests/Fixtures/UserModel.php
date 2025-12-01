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
 * Test fixture model using 'id' as primary key.
 *
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class UserModel extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $primaryKey = 'id';
}
