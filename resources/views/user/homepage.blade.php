@extends('layouts.app')

@section('content')

<!-- HERO -->
<section class="py-10">
    <h1 class="text-center text-4xl md:text-5xl font-bold text-red-600 drop-shadow-md">
        Kripik Tempe Inti Sari
    </h1>

    <img src="/assets/logo.jpg"
         class="mx-auto mt-8 w-64 md:w-80 rounded-full shadow-lg">
</section>

<!-- PRODUK UTAMA -->
<section id="produk" class="bg-red-600 text-white rounded-3xl mx-auto w-11/12 p-8 mt-16">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Produk Utama</h2>
        <a href="{{ route('katalog.index') }}" class="text-yellow-300 hover:underline">
            Lihat produk >
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">

        {{-- PRODUK CARD SERAGAM --}}
        @php
            $utama = [
                ['img'=>'logo.jpg','stok'=>10,'nama'=>'Kripik Tempe Original','harga'=>'15.000'],
                ['img'=>'kripik_dage.jpg','stok'=>8,'nama'=>'Kripik Tempe Pedas','harga'=>'16.000'],
                ['img'=>'sale_goreng.jpg','stok'=>5,'nama'=>'Kripik Tempe Barbeque','harga'=>'17.000'],
                ['img'=>'bolu_ketapang.jpg','stok'=>12,'nama'=>'Kripik Tempe Balado','harga'=>'18.000'],
            ];
        @endphp

        @foreach($utama as $item)
        <div class="bg-white text-gray-700 rounded-2xl p-4 shadow-md hover:scale-105 transition
                    flex flex-col">
            
            <div class="w-full h-40 rounded-xl overflow-hidden mb-3">
                <img src="/assets/{{ $item['img'] }}"
                     class="w-full h-full object-cover">
            </div>

            <small class="text-red-500">Stok: {{ $item['stok'] }}</small>
            <p class="font-bold">{{ $item['nama'] }}</p>
            <p>Rp. {{ $item['harga'] }}</p>
        </div>
        @endforeach

    </div>
</section>

<!-- HALAL -->
<section id="halal" class="w-11/12 mx-auto mt-24 flex flex-col md:flex-row gap-12 items-center">

    <!-- Gambar portrait proporsional -->
    <img src="/assets/halal.jpg"
         class="w-full md:w-[35%] h-[420px] object-cover rounded-3xl shadow-lg">

    <div class="md:w-1/2">
        <h2 class="text-4xl font-bold mb-4 leading-snug">Halal is My Life</h2>

        <p class="leading-relaxed text-lg mb-4">
            Penerapan “Halal is My Life” dari Tempe Kripik Inti Sari memiliki makna universal. Pemaknaan halal 
            sebagai “diperbolehkan” menjadi dasar Tempe Kripik Inti Sari untuk menghadirkan makanan yang terjaga 
            baik dari proses pemilihan bahan hingga produksinya.
        </p>

        <a href="https://maps.app.goo.gl/KCiQp3nwadY4ySj29" target="_blank"
            class="bg-yellow-400 px-8 py-3 rounded-xl mt-4 font-bold hover:bg-yellow-500 inline-block">
            Lihat Sertifikat
        </a>
    </div>
</section>

<!-- TENTANG KAMI -->
<section id="tentang" class="w-11/12 mx-auto mt-24 flex flex-col md:flex-row gap-12 items-center">

    <div class="md:w-1/2">
        <h2 class="text-4xl font-bold mb-4 leading-snug">Tentang Kami</h2>

        <p class="leading-relaxed text-lg mb-4">
            Tempe Kripik Inti Sari bergerak di bidang usaha kuliner khas daerah dengan produk unggulan Tempe Kripik.
            Konsistensi rasa dan mutu selalu menjadi prioritas utama dalam setiap proses produksinya.
        </p>

        <p class="leading-relaxed text-lg">
            Berlokasi di Purwokerto Selatan, toko kami dipilih untuk memudahkan masyarakat berbelanja produk keripik
            baik untuk konsumsi sendiri maupun oleh-oleh.
        </p>

        <p class="font-semibold text-orange-600 mt-4 text-lg">Buka Jam 09:00 - 21:00</p>

        <a href="https://maps.app.goo.gl/KCiQp3nwadY4ySj29" target="_blank"
            class="bg-yellow-400 px-8 py-3 rounded-xl mt-4 font-bold hover:bg-yellow-500 inline-block">
            Lihat Peta
        </a>
    </div>

    <!-- Gambar portrait -->
    <img src="/assets/toko.jpg"
         class="md:w-[35%] h-[420px] object-cover rounded-3xl shadow-lg">
</section>

<!-- KATALOG PRODUK -->
<section class="relative w-11/12 mx-auto mt-24 rounded-3xl overflow-hidden">

    <!-- BACKGROUND GAMBAR -->
    <div class="relative">
        <img src="/assets/etalase.png"
             class="w-full h-[360px] object-cover">

        <!-- SHADOW DI ATAS GAMBAR SAJA -->
        <div class="absolute top-0 left-0 w-full h-32 
                    bg-gradient-to-b from-gray-200/90 to-transparent">
        </div>
    </div>

    <!-- TEKS DI POSISI TIDAK FULL DI ATAS GAMBAR -->
    <div class="absolute inset-0 flex flex-col items-center text-center">

        <!-- Spacer agar teks turun sedikit (sekitar 30%) -->
        <div class="h-[30%]"></div>

        <p class="leading-relaxed text-xl font-semibold text-gray-800 max-w-2xl drop-shadow">
            Produk kami tidak hanya Tempe Kripik, tetapi juga Kripik Dage, 
            Kripik Kedelai Hitam, dan Kripik Gadung.
        </p>

        <a href="{{ route('katalog.index') }}"
            class="bg-yellow-400 px-8 py-4 rounded-xl text-lg font-bold hover:bg-yellow-500 mt-6 shadow-lg">
            Katalog Produk
        </a>

    </div>

</section>


@endsection