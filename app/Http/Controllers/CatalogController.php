<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;

class CatalogController extends Controller
{
    
    public function index(Request $request)
    {
        $produks = Produk::query();

        if ($request->has('kategori')) {
            $produks->whereIn('kategori_id', $request->kategori);
        }

        return view('user.catalog', [
            'produks' => $produks->paginate(8)->withQueryString(),
            'kategoris' => Kategori::all(),
        ]);
    }
}

