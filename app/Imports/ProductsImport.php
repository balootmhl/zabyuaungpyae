<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Group;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ProductsImport implements OnEachRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();
        $category = Category::firstOrCreate(['code' => $row[2], 'name' => $row[3]]);
        $group = Group::firstOrCreate(['name' => $row[7]]);
        $product = new Product();
        $product->code = $row[0];
        $product->name = $row[1];
        $product->category_id = $category->id;
        $product->buy_price = $row[4];
        $product->sale_price = $row[5];
        $product->quantity = $row[6];
        $product->group_id = $group->id;
        $product->save();
    }
}
