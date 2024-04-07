<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\Language;
use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Fixtures\Factories\SomeObjectDetailFactory;

/**
 * @method static SomeObjectDetailFactory factory(mixed $parameters = null)
 * @property Region $rgion
 * @property Language $language
 */
class SomeObjectDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['count', 'name', 'region', 'language', 'description'];

    protected $casts = [
        'region' => Region::class,
        'language' => Language::class,
    ];
}
