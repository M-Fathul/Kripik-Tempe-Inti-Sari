<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        // Nanti tinggal hubungkan ke table "products"
        $products = Product::all(); 

        return view('user.catalog', compact('products'));
    }
}

