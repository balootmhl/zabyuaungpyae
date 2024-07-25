<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Group extends Model
{
    // use HasFactory;

    use AsSource, Attachable, Filterable;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'name',
        'user_id'
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    // protected $allowedFilters = [
    //     'name',
    // ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
