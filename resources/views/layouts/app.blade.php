<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kripik Tempe Inti Sari' }}</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    
</head>

<body class="bg-background">

    @include('layouts.header')

    <main class="pt-20">
        @yield('content')
    </main>

    @include('layouts.footer')

</body>
</html>
