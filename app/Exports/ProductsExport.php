<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    protected $id;

    function __construct($id) {
            $this->id = $id;
    }
    
    
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return Product::select(
    //         'code',
    //         'name',
    //         'category_id',
    //         'buy_price',
    //         'sale_price',
    //         'quantity',
    //         'group_id')->orderBy('id')->get();
    // }

    public function view(): View
    {
        return view('export.products', [
            'products' => Product::where('user_id', $this->id)->get(),
        ]);
    }
}
