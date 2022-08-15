<?php

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static ItemFactory factory(...$parameters)
 * @method static Builder|Item newModelQuery()
 * @method static Builder|Item newQuery()
 * @method static Builder|Item query()
 * @method static Builder|Item whereCompleted($value)
 * @method static Builder|Item whereCreatedAt($value)
 * @method static Builder|Item whereDescription($value)
 * @method static Builder|Item whereId($value)
 * @method static Builder|Item whereName($value)
 * @method static Builder|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    use HasFactory;

    protected $table = 'lists';
    protected $fillable = [
        'name',
        'description',
        'completed',
    ];


    public static function getItemsQuery(array $filters = []): Builder
    {
        return self::query()
            ->when($filters['name'],
                fn($query) => $query->where('name', $filters['name'])
            )
            ->when($filters['completed'],
                fn($query) => $query->where('completed', $filters['completed'])
            );
    }

    public static function get(Item $item): Item|null
    {
        return self::query()
            ->where('id', $item->id)
            ->first();
    }
}
