@extends('layouts.app')

@section('content')

{{-- HERO SECTION --}}
<div class="max-w-7xl mx-auto min-h-screen">
<section class="w-11/12 mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
    
    <div>
        <h1 class="text-4xl md:text-5xl font-bold mb-6">Kunjungi Toko Kami</h1>

        <p class="leading-relaxed mb-6 text-gray-700">
            Kami juga bersedia bekerjasama untuk menerima produk anda untuk di jual di toko kami 
            yang berlokasi di Jl. Pramuka No.240, Samudra, Purwokerto Kidul, Kec. Purwokerto Sel., 
            Kabupaten Banyumas, Jawa Tengah 53147
        </p>

        <a href="https://wa.me/6281395863537" 
           class="bg-yellow-400 px-6 py-3 rounded-xl font-semibold hover:bg-yellow-500">
           Hubungi Kami
        </a>
    </div>
    <div class="relative">
        <img src="/assets/etalase.png"
         class="rounded-r-3xl w-auto h-[420px] object-cover">

        <div class="absolute top-0 left-0 w-full h-full 
                    bg-linear-to-r from-background via-background/40 to-transparent via-40%">
        </div>
    </div>

    
</section>

{{-- PRODUK TITLE --}}
<section class="mt-20 text-center">
    <h2 class="text-2xl font-bold text-red-600">Produk di Toko Kami</h2>

    <div class="flex justify-center gap-6 mt-4">
        <button class="text-red-600 font-semibold border-b-4 border-yellow-400 pb-1">Semua</button>
        <button class="text-gray-500 hover:text-red-600">Asin</button>
        <button class="text-gray-500 hover:text-red-600">Manis</button>
    </div>
</section>

{{-- PRODUK GRID --}}
<section class="w-11/12 mx-auto mt-10 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6 px-16">

    @foreach($produks as $produk)
        <div class="bg-white rounded-r-3xl col-span-2 shadow-md flex pr-6">
            <div class="w-4 h-full bg-secondary"></div>

            <img src="{{ asset('storage/' . $produk->image) }}"
                class="w-full h-24 object-cover">

            <div class="p-4 flex flex-col w-full justify-center">
                <p class="font-semibold">
                    {{ $produk->nama_produk }}
                </p>

                <div class="flex justify-between w-full mt-2">
                    <p class="font-light text-sm">
                        Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}
                    </p>
                    <small class="text-light">
                        Stok: {{ $produk->stok }}
                    </small>
                </div>
            </div>

        </div>
    @endforeach

    <div class="col-span-2 md:col-span-4 flex justify-center mt-6">
        {{ $produks->links() }}
    </div>

</section>
</div>
@endsection
