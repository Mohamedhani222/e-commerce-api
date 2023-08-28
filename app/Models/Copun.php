<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Copun
 *
 * @property int $id
 * @property string $code
 * @property int $discount
 * @property string $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Copun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Copun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Copun query()
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Copun whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Copun extends Model
{
    use HasFactory;
}
