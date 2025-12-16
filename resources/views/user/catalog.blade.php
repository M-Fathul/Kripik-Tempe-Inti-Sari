@extends('layouts.app')

@section('content')

{{-- HERO SECTION --}}
<section class="w-11/12 mx-auto mt-16 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
    
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

    <img src="/assets/etalase.png"
         class="rounded-3xl shadow-xl w-full object-cover">
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
<section class="w-11/12 mx-auto mt-10 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">

    @foreach(range(1,8) as $i)
        <div class="bg-white p-4 rounded-3xl shadow-md hover:shadow-xl transition">
            <img src="/assets/keripik.jpg" class="rounded-2xl mb-4 w-full">
            
            <small class="text-orange-500">Stok: 10</small>
            <p class="font-semibold mt-1">Kripik Tempe</p>
            <p class="text-gray-500 text-sm">Rp. xxxxx</p>
        </div>
    @endforeach

</section>

@endsection
