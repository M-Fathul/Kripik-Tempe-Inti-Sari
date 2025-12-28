@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto min-h-screen">
        <!-- HERO -->
        <section class="py-10">
            <h1 class="text-center text-4xl md:text-5xl font-bold text-red-600 drop-shadow-md">
                Kripik Tempe Inti Sari
            </h1>
            <!-- 
        <img src="/assets/hero.png"
             class="mx-auto mt-8 w-64 md:w-80"> -->
        </section>

        <!-- PRODUK UTAMA -->
        <section id="produk"
            class="bg-red-600 text-white rounded-3xl mx-auto w-11/12 mt-16 relative h-65 md:h-60 overflow-visible pt-8">
            <div class="flex flex-col items-center">
                <h2 class="text-2xl font-semibold text-yellow-300 justify-center">Produk Utama</h2>
            </div>
            <div class="absolute inset-x-0 bottom-0 translate-y-1/2 mt-6 w-11/12 mx-auto">
                <div id="produk-scroll" class="flex md:grid md:grid-cols-4 p-4 gap-6
                        overflow-x-auto md:overflow-visible
                        snap-x snap-mandatory scroll-smooth
                        no-scrollbar">
                    @php
                        $utama = [
                            ['img' => 'logo.jpg', 'nama' => 'Kripik Tempe Original'],
                            ['img' => 'kripik_dage.jpg', 'nama' => 'Kripik Tempe Pedas'],
                            ['img' => 'sale_goreng.jpg', 'nama' => 'Kripik Tempe Barbeque'],
                            ['img' => 'bolu_ketapang.jpg', 'nama' => 'Kripik Tempe Balado'],
                        ];
                    @endphp

                    @foreach($utama as $item)
                        <div class="min-w-full md:min-w-0 snap-center
                                bg-white text-gray-700 rounded-2xl p-4 shadow-lg flex flex-col items-center">

                            <div class="w-full h-48 rounded-t-xl mb-3">
                                <img src="/assets/{{ $item['img'] }}" class="w-full h-full rounded-t-xl object-cover">
                            </div>
                            <p class="font-bold">{{ $item['nama'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="scrollProduk(-1)" class="md:hidden z-10
                            bg-white/90 rounded-full p-3 shadow text-black">
                            ‹
                        </button>
                        <button onclick="scrollProduk(1) " class="md:hidden z-10
                            bg-white/90 rounded-full p-3 shadow text-black">
                            ›
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative w-full mx-auto mt-70 ">

            <!-- BACKGROUND GAMBAR -->
            <div class="relative">
                <img src="/assets/etalase.png" class="w-full h-full object-cover rounded-b-2xl">

                <div class="absolute top-0 left-0 w-full h-full 
                        bg-linear-to-b from-background via-background/40 to-transparent via-60%">
                </div>
            </div>

            <!-- TEKS DI POSISI TIDAK FULL DI ATAS GAMBAR -->
            <div class="absolute inset-0 top-0 -translate-y-4/5 md:-translate-y-1/4 flex flex-col items-center text-center">
                <p class="leading-relaxed md:text-xl text-gray-800 max-w-4xl drop-shadow">
                    Produk yang kami hasilkan tidak hanya sekadar Tempe Kripik saja, tetapi juga beberapa produk keripik
                    lainnya seperti Kripik Dage, Kripik Kedelai Hitam, dan Kripik Gadung. Semua produk keripik tersebut
                    diharapkan bisa membuat masyarakat semakin leluasa memilih camilan kesukaan
                </p>

                <a href="{{ route('katalog.index') }}" type="button"
                    class="bg-secondary text-white px-4 py-4 rounded-xl text-sm hover:bg-yellow-500 mt-6 shadow-lg">
                    Katalog Produk
                </a>

            </div>

        </section>

        <section id="halal" class="w-11/12 mx-auto mt-24 grid grid-cols-1 md:grid-cols-3 gap-12 items-center">

            <div
                class="md:col-span-1 bg-linear-to-b from-primary via-primary/0 to-primary/0 via-75% h-[600px] rounded-full p-4 flex items-end justify-center">
                <img src="/assets/halal.png" class="w-full md:w-auto h-[330px] object-cover">
            </div>
            <div class="md:col-span-2">
                <h2 class="text-4xl font-bold mb-4 leading-snug">Halal is My Life</h2>

                <p class="leading-relaxed md:text-lg mb-4">
                    Penerapan “Halal is My Life” dari Tempe Kripik Inti Sari memiliki makna yang universal. Pemaknaan halal
                    yang diartikan sebagai “diperbolehkan” menjadi dasar Tempe Kripik Inti Sari untuk menghadirkan makanan
                    khas yang terjaga dari proses pemilihan bahannya dan proses produksinya. Makanan berjenis keripik yang
                    tersedia merupakan makanan yang terjaga baik kualitasnya sehingga baik untuk dikonsumsi semua orang
                </p>

                <a href="https://bpjph.halal.go.id/sertifikat-halal/sertifikat?nama_pelaku_usaha=KRIPIK+TEMPE+INTISARI&page=1"
                    target="_blank" type="button"
                    class="bg-secondary text-white px-4 py-4 rounded-xl text-sm hover:bg-yellow-500 mt-6 shadow-lg">
                    Lihat Sertifikat
                </a>
            </div>
        </section>

        <!-- TENTANG KAMI -->
        <section id="tentang" class="w-11/12 mx-auto mt-24 grid grid-cols-1 md:grid-cols-3 gap-12 items-center">

            <div class="p-4 md:col-span-2">
                <h2 class="text-4xl font-bold mb-4 leading-snug">Tentang Kami</h2>

                <p class="leading-relaxed md:text-lg mb-4">
                    Tempe Kripik Inti Sari bergerak dibidang usaha kuliner khas daerah dengan produk unggulan Tempe Kripik.
                    Konsistensi Tempe Kripik Inti Sari selalu diikuti dengan jaminan mutu dan rasa. Produk Tempe Kripik yang
                    renyah dan gurih dari Inti Sari akan menghadirkan keceriaan setiap waktu. Makanan khas dari Purwokerto
                    ini merupakan makanan yang tergolong kudapan atau camilan.
                </p>

                <p class="leading-relaxed md:text-lg">
                    Tempe Kripik Inti Sari beralamat di Kec. Purwokerto Sel., Kabupaten Banyumas, Jawa Tengah. Lokasi yang
                    sangat mudah dijangkau memang kami pilih untuk memudahkan masyarakat untuk berbelanja produk keripik
                    baik untuk dikonsumsi sendiri maupun untuk dijadikan oleh-oleh.
                </p>

                <p class="font-semibold text-orange-600 my-4 text-lg">Buka Jam 09:00 - 21:00</p>

                <a href="https://maps.app.goo.gl/KCiQp3nwadY4ySj29" target="_blank" type="button"
                    class="bg-secondary text-white px-4 py-4 rounded-xl text-sm hover:bg-yellow-500 mt-6 shadow-lg">
                    Lihat Peta
                </a>
            </div>

            <!-- Gambar portrait -->
            <img src="/assets/toko.jpg" class="md:w-auto h-full object-cover rounded-3xl shadow-lg md:col-span-1">
        </section>
    </div>
    <!-- KATALOG PRODUK -->



@endsection