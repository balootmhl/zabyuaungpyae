<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Group;
use App\Models\Saleitem;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Platform\Models\User;
use Orchid\Screen\AsSource;

class Product extends Model {
	// use HasFactory;

	use AsSource, Attachable, Filterable;

	/**
	 * @var array
	 */
	protected $fillable = [
		'code',
		'name',
		'category_id',
		'buy_price',
		'sale_price',
		'quantity',
		'group_id',
		'user_id',
	];

	/**
	 * Name of columns to which http sorting can be applied
	 *
	 * @var array
	 */
	protected $allowedSorts = [
		'code',
		'name',
		'buy_price',
		'category_id',
		'sale_price',
		'quantity',
	];

	/**
	 * Name of columns to which http filter can be applied
	 *
	 * @var array
	 */
	protected $allowedFilters = [
		'code',
		'name',
		'buy_price',
		'sale_price',
		'quantity',

	];

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function group() {
		return $this->belongsTo(Group::class);
	}

	public function saleitems() {
		return $this->hasMany(Saleitem::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function branch() {
		return $this->belongsTo(Branch::class);
	}

}
