<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function getPrice(Request $request, $id = null)
    {
        $getPrice = $request->get('id', $id);
        $price = DB::table('products')->where('id', $getPrice)->get();
        return response()->json($price);
    }
}
