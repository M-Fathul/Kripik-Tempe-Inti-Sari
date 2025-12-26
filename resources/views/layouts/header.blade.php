<header x-data="{ open: false }"
        class="w-full fixed top-0 /70  z-50">

    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center backdrop-blur border-b">

        <a href="{{ route('user.homepage') }}" class="font-semibold text-lg hover:text-red-600">Kripik Tempe Inti Sari</a>

        <nav class="hidden md:flex space-x-6 text-sm">
            <a href="{{ route('katalog.index') }}" class="hover:text-red-600">Katalog Produk</a>
            <a href="{{ route('user.homepage') }}#halal" class="hover:text-red-600">Sertifikat Halal</a>
            <a href="{{ route('user.homepage') }}#tentang" class="hover:text-red-600">Tentang Kami</a>
        </nav>

        <button @click="open = true"
                class="md:hidden flex items-center focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                 stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div x-show="open"
         x-transition.opacity
         @click="open = false"
         class="fixed inset-0 bg-black/40 z-40">
    </div>

    <aside x-show="open"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed top-0 right-0 w-72 h-full bg-white z-50 shadow-xl p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="font-semibold text-lg">Menu</h2>
            <button @click="open = false">
                ✕
            </button>
        </div>

        <nav class="flex flex-col space-y-4 text-sm">
            <a @click="open = false" href="#produk" class="hover:text-red-600">
                Produk
            </a>
            <a @click="open = false" href="#halal" class="hover:text-red-600">
                Sertifikat Halal
            </a>
            <a @click="open = false" href="#tentang" class="hover:text-red-600">
                Tentang Kami
            </a>
        </nav>
    </aside>
</header>
