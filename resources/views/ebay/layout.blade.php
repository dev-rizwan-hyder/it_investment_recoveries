<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'eBay Summary')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        input, textarea { outline: none; }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-stretch gap-3 px-3 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-6 sm:py-4 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('images/it_logo.png') }}" alt="IT Logo" class="h-9 w-auto flex-shrink-0 object-contain sm:h-10">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 sm:text-xs">Denver Discount Computers</p>
                    <h1 class="truncate text-lg font-extrabold tracking-tight text-slate-950 sm:text-xl">@yield('page-title', 'eBay Summary')</h1>
                </div>
            </div>
            @yield('header-action')
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">
        @if (session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @php
            $allErrors = isset($errors)
                ? collect($errors->getBags())->flatMap(fn ($bag) => $bag->all())->all()
                : [];
        @endphp

        @if (!empty($allErrors))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                <p class="mb-2">Please fix the following:</p>
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($allErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>

</html>
