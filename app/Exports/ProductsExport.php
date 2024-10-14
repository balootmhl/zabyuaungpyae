<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
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
        $user = User::findOrFail($this->id);
        return view('export.products', [
            'products' => Product::where('branch_id', $user->branch_id)->get(),
        ]);
    }
}
