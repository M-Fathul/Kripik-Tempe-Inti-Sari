<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class CatalogController extends Controller
{
    public function index()
    {
        // Nanti tinggal hubungkan ke table "products"
        $produks = Produk::all(); 

        return view('user.catalog', compact('produks'));
    }
}

