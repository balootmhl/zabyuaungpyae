<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Response;

class RecipeController extends Controller
{
    public function getPrice()
    {
        $getPrice = $_GET['id'];
        $price = DB::table('products')->where('id', $getPrice)->get();
        return Response::json($price);
    }
}
