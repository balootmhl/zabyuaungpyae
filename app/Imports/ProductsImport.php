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
        $product = new Product();
        $product->code = $row[0];
        $product->name = $row[1];
        $category = Category::firstOrCreate(['name' => $row[2]]);
        $product->category_id = $category->id;
        $product->buy_price = $row[3];
        $product->sale_price = $row[4];
        if($row[5]=="" || $row[5]==null) {
            $product->quantity = 0;
        } else {
            $product->quantity = $row[5];
        }

        if($row[5]=="" || $row[5]==null) {
            $group = Group::firstOrCreate(['name' => 'ZZ']);
        } else {
            $group = Group::firstOrCreate(['name' => $row[6]]);
        }
        
        $product->group_id = $group->id;
        $product->save();
    }
}
