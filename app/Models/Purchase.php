<?php

namespace App\Models;

use App\Models\Purchaseitem;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Platform\Models\User;
use Orchid\Screen\AsSource;

class Purchase extends Model
{
    // use HasFactory;

    use AsSource, Attachable, Filterable;

    /**
     * @var array
     */
    protected $fillable = [
        'date',
        'user_id',
        'supplier_id',
        'invoice_no',
        'discount',
        'received',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'date',
        'user_id',
        'supplier_id',
        'invoice_no',
        'sub_total',
        'discount',
        'grand_total',
        'received',
        'remained',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'date',
        'user_id',
        'supplier_id',
        'invoice_no',
        'sub_total',
        'discount',
        'grand_total',
        'received',
        'remained',
    ];

    public function purchaseitems()
    {
        return $this->hasMany(Purchaseitem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
