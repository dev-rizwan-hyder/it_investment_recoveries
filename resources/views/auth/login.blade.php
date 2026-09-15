<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login | IT Investment Recoveries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-bg { background: radial-gradient(circle at top right, #0f172a, #1e293b); }
        .input-focus:focus { box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); border-color: #2563eb; }
    </style>
</head>
<body class="bg-white flex min-h-screen antialiased">
    <div class="hidden lg:flex w-[45%] gradient-bg items-center justify-center p-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500 rounded-full blur-[120px]"></div>
        </div>
        <div class="relative z-10 text-center lg:text-left">
            <h1 class="text-5xl font-bold text-white leading-tight mb-6">
                IT INVESTMENT <br>
                <span class="text-blue-400 font-extrabold">RECOVERIES.</span>
            </h1>
            <p class="text-slate-400 text-xl leading-relaxed max-w-sm">
                Customer Portal for IT assets, data destruction certificates, and order tracking.
            </p>
        </div>
    </div>

    <div class="w-full lg:w-[55%] flex flex-col items-center justify-center p-8 lg:p-20">
        <div class="max-w-[420px] w-full">
            <div class="flex flex-col items-center text-center mb-10">
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Customer Login</h2>
                <p class="text-slate-500 font-medium">Sign in to your customer portal account.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm flex items-center justify-center gap-3">
                    <i class="fas fa-circle-exclamation"></i>
                    <p class="font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Google Sign In Button -->
            <a href="{{ route('auth.google') }}"
                class="w-full bg-white hover:bg-slate-50 text-slate-800 font-bold py-4 rounded-2xl border border-slate-200 transition-all duration-300 flex items-center justify-center gap-3 shadow-sm active:scale-[0.98]">
                <i class="fa-brands fa-google text-[#4285f4]"></i>
                <span class="tracking-wide">Sign in with Google</span>
            </a>

            <div class="my-6 flex items-center gap-4">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">or</span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <!-- Sign In Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Email</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="far fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                            placeholder="customer@company.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[13px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-11 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" value="on"
                        class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer">
                    <label for="remember" class="ml-2 text-[13px] font-medium text-slate-600 cursor-pointer">Remember me</label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-3 shadow-xl shadow-blue-100 active:scale-[0.98]">
                    <span class="tracking-wide">Sign In to Customer Portal</span>
                    <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-600 text-sm font-medium">
                    Don't have an account? 
                    <a href="{{ route('register.page') }}" class="text-blue-600 font-bold hover:text-blue-700 transition-colors ml-1">
                        Register Here
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
