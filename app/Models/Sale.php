<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Saleitem;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Platform\Models\User;
use Orchid\Screen\AsSource;

class Sale extends Model
{
    // use HasFactory;

    use AsSource, Attachable, Filterable;

    /**
     * @var array
     */
    protected $fillable = [
        'date',
        'user_id',
        'customer_id',
        'status',
        'is_cash',
        'discount',
        'received',
        'remarks',
        'custom_name',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'date',
        'user_id',
        'customer_id',
        'invoice_no',
        'status',
        'is_cash',
        'sub_total',
        'discount',
        'grand_total',
        'received',
        'remained',
        'remarks',
        'custom_name',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'date',
        'user_id',
        'customer_id',
        'invoice_no',
        'status',
        'is_cash',
        'sub_total',
        'discount',
        'grand_total',
        'received',
        'remained',
        'remarks',
        'custom_name',
    ];

    public function saleitems()
    {
        return $this->hasMany(Saleitem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
