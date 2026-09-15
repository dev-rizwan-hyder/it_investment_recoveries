<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register | IT Investment Recoveries</title>
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
                Create a customer account to manage your IT disposal & data destruction records.
            </p>
        </div>
    </div>

    <div class="w-full lg:w-[55%] flex flex-col items-center justify-center p-8 lg:p-20">
        <div class="max-w-[420px] w-full">
            <div class="flex flex-col items-center text-center mb-8">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Create Customer Account</h2>
                <p class="text-slate-500 font-medium">Enter your details to register.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm flex items-center justify-center gap-3">
                    <i class="fas fa-circle-exclamation"></i>
                    <p class="font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="space-y-1">
                    <label class="text-[12px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                        placeholder="John Doe">
                </div>

                <div class="space-y-1">
                    <label class="text-[12px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                        placeholder="name@company.com">
                </div>

                <div class="space-y-1">
                    <label class="text-[12px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                        placeholder="••••••••">
                </div>

                <div class="space-y-1">
                    <label class="text-[12px] font-bold text-slate-700 uppercase tracking-widest ml-1 block">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all text-slate-900 placeholder-slate-400 font-medium input-focus"
                        placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-3 shadow-xl shadow-blue-100 active:scale-[0.98]">
                    <span class="tracking-wide">Register Account</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-600 text-sm font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:text-blue-700 transition-colors ml-1">
                        Sign In Here
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
