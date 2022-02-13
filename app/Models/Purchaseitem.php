<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Purchaseitem extends Model
{
    // use HasFactory;

    use AsSource, Attachable, Filterable;

    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'purchase_id',
        'quantity',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'product_id',
        'purchase_id',
        'quantity',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'product_id',
        'purchase_id',
        'quantity',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
