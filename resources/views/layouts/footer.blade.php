<footer class="bg-background py-12">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">

        <div>
            <h2 class="font-bold mb-3">Kripik Inti Sari</h2>
            <a href="https://maps.app.goo.gl/KCiQp3nwadY4ySj29" target="_blank" class="hover:text-primary text-sm text-gray-600">
                Jl. Pramuka No.240, Somodro,<br>
                Purwokerto Kidul, Banyumas, Jawa Tengah<br>
                53147
            </a>
        </div>

        <div>
            <h2 class="font-bold mb-3">Kontak</h2>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>
                    <a href="https://wa.me/6281226000034" class="hover:text-primary">Whatsapp</a>
                </li>
                <li>
                    <a href="https://www.instagram.com/intisarikripik/" class="hover:text-primary">Instagram</a>
                </li>
            </ul>
        </div>

        <div>
            <h2 class="font-bold mb-3">Usaha</h2>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>
                    <a href="{{ route('katalog.index') }}" class="hover:text-primary">Produk
                </li>
                <li>
                    <a href="{{ route('user.homepage') }}#halal" class="hover:text-primary">Sertifikat Halal</a>
                </li>
                <li>
                    <a href="{{ route('user.homepage') }}#tentang" class="hover:text-primary">Tentang Kami</a>
                </li>
            </ul>
        </div>

    </div>

    <div class="text-center mt-6 text-gray-500 text-sm">
        © 2025 Kripik Tempe Inti Sari
    </div>
</footer>