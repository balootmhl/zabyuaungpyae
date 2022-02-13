<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Category extends Model
{
    // use HasFactory;

    use AsSource, Attachable, Filterable;

    /**
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'code',
        'name',
        'category_id',
        'buy_price',
        'sale_price',
        'quantity',
        'group_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
