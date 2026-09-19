<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register | IT Investment Recoveries</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-bg { background: radial-gradient(circle at top right, #0f172a, #1e293b); }
        .input-focus:focus { box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12); border-color: #2563eb; }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen antialiased selection:bg-blue-500 selection:text-white">
    <!-- Left Hero Banner -->
    <div class="hidden lg:flex w-[45%] gradient-bg items-center justify-center p-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500 rounded-full blur-[120px]"></div>
        </div>
        <div class="relative z-10 max-w-md text-left space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-user-plus text-blue-400"></i> Account Registration
            </div>
            <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight">
                IT INVESTMENT <br>
                <span class="text-blue-400 font-black">RECOVERIES.</span>
            </h1>
            <p class="text-slate-300 text-base leading-relaxed font-medium">
                Create your customer portal account to track pallet intake shipments, data sanitization certificates, and asset processing status.
            </p>
        </div>
    </div>

    <!-- Right Form Section -->
    <div class="w-full lg:w-[55%] flex flex-col items-center justify-center p-6 sm:p-12 lg:p-20">
        <div class="max-w-[420px] w-full">
            <div class="flex flex-col items-center text-center mb-8">
                <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2 mb-6">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white font-bold">
                        <i class="fa-solid fa-cube"></i>
                    </span>
                    <span class="font-extrabold text-slate-900 text-lg">IT Recoveries</span>
                </a>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Register Account</h2>
                <p class="text-slate-500 text-sm font-medium">Create a new customer portal account.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl text-rose-700 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
                    <i class="fas fa-circle-exclamation text-rose-500 text-lg shrink-0"></i>
                    <p class="font-bold">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus text-sm"
                        placeholder="John Doe">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus text-sm"
                        placeholder="name@company.com">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus text-sm"
                        placeholder="••••••••">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus text-sm"
                        placeholder="••••••••">
                </div>

                <button type="submit" class="w-full mt-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                    <span class="tracking-wide text-sm">Register Account</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-slate-200/80">
                <p class="text-slate-600 text-xs sm:text-sm font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-extrabold hover:text-blue-700 transition-colors ml-1">
                        Sign In Here
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

