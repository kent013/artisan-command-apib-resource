<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\Language;
use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static SomeObjectFactory factory(mixed $parameters = null)
 * @property Region $rgion
 * @property Language $language
 */
class SomeObject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['count', 'name', 'region', 'language', 'description'];

    protected $casts = [
        'region' => Region::class,
        'language' => Language::class,
    ];

    /**
     * @return HasMany<SomeObjectDetail>
     */
    public function details(): HasMany
    {
        return $this->hasMany(SomeObjectDetail::class);
    }
}
