<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kripik Tempe Inti Sari' }}</title>

    @vite('resources/css/app.css')
</head>

<body class="antialiased">

    @include('layouts.header')

    <main class="pt-24">
        @yield('content')
    </main>

    @include('layouts.footer')

</body>
</html>
