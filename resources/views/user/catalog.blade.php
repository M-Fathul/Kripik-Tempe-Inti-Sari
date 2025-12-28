@extends('layouts.app')

@section('content')


<div class="max-w-7xl mx-auto min-h-screen">
    <section class="mt-10 text-center">
        <h2 class="text-2xl font-bold mb-4 text-red-600">Produk di Toko Kami</h2>
        <form method="GET" action="{{ route('katalog.index') }}"
        class="flex flex-col gap-3">
            <div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk..."
                    class="px-4 py-2 rounded-full border text-sm
                        border-gray-300 focus:ring-2 focus:ring-red-500
                        focus:outline-none">
                
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-full text-sm hover:bg-red-700">
                    Cari
                </button>
            </div>
            <div class="flex gap-2 flex-wrap justify-center items-center ">
                @foreach ($kategoris as $kategori)
                    <label class="cursor-pointer">
                        <input type="checkbox"
                            name="kategori[]"
                            value="{{ $kategori->id }}"
                            class="peer hidden"
                            {{ in_array($kategori->id, request('kategori', [])) ? 'checked' : '' }}
                            onchange="this.form.submit()">

                        <span class="px-4 py-2 rounded-full border text-sm
                                    text-gray-600 border-gray-300
                                    peer-checked:bg-red-600
                                    peer-checked:text-white
                                    peer-checked:border-red-600
                                    hover:bg-red-50 transition">
                            {{ $kategori->nama_kategori }}
                        </span>
                    </label>
                @endforeach
            </div>
        </form>
    </section>

    <section class="w-11/12 mx-auto mt-10 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6 md:px-16">
        @forelse($produks as $produk)
            <div class="bg-white h-28 rounded-r-3xl col-span-2 shadow-md flex pr-6">
                <div class="w-4 h-full bg-secondary"></div>
                <img src="{{ $produk->image
                        ? asset('storage/' . $produk->image)
                        : asset('assets/image_no.png') }}"
                    class="w-full h-full object-cover">
                    
                <div class="p-4 flex flex-col w-full justify-center">
                    <p class="font-semibold">
                        {{ $produk->nama_produk }}
                    </p>

                    <div class="flex justify-between w-full mt-2">
                        <p class="font-light text-sm">
                            {{ $produk->kategori?->nama_kategori ?? '-' }}
                        </p>
                        <small class="text-light">
                            Stok: {{ $produk->stok }}
                        </small>
                    </div>
                    <div class="flex justify-between w-full">
                        <p class="font-light text-sm">
                            Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}
                        </p>
                        <small class="font-light text-sm">
                            Terjual: {{ $produk->total_terjual }}
                        </small>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-span-2 md:col-span-4 flex justify-center">
                <div class=" p-8 text-center w-full md:w-1/2">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Produk tidak ditemukan
                    </h3>
                    <p class="text-gray-400 mt-2">
                        Silakan coba filter atau pencarian lain
                    </p>
                </div>
            </div>
        @endforelse

        <div class="col-span-2 mb-10 md:col-span-4 flex justify-center mt-6">
            {{ $produks->withQueryString()->links() }}
        </div>

    </section>

    <section class="w-11/12 mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        
        <div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Kunjungi Toko Kami</h1>

            <p class="leading-relaxed mb-6 text-gray-700">
                Kami juga bersedia bekerjasama untuk menerima produk anda untuk di jual di toko kami 
                yang berlokasi di Jl. Pramuka No.240, Samudra, Purwokerto Kidul, Kec. Purwokerto Sel., 
                Kabupaten Banyumas, Jawa Tengah 53147
            </p>

            <a href="https://wa.me/6281226000034" 
            class="bg-yellow-400 px-6 py-3 rounded-xl font-semibold hover:bg-yellow-500">
            Hubungi Kami
            </a>
        </div>
        <div class="relative">
            <img src="/assets/etalase.png"
            class="rounded-b-3xl md:rounded-r-3xl w-auto h-[420px] object-cover">

            <div class="absolute top-0 left-0 w-full h-full 
                        bg-linear-to-b md:bg-linear-to-r from-background via-background/40 to-transparent via-50%">
            </div>
        </div>
    </section>
</div>
@endsection
