<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;

class CatalogController extends Controller
{

    public function index(Request $request)
    {
        $kategoris = Kategori::all();

        $produks = Produk::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nama_produk', 'like', "%{$search}%");
            })
            ->when($request->kategori, function ($query, $kategori) {
                $query->whereIn('kategori_id', $kategori);
            })
            ->with('kategori')
            ->paginate(8);

        return view('user.catalog', compact('produks', 'kategoris'));
    }
}

