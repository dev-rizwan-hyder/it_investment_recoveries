<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about-us', function () {
    return view('about');
})->name('about');

Route::get('/about', function () {
    return redirect()->route('about');
});

Route::get('/residential-pickup', function () {
    return view('ewaste');
})->name('ewaste');

Route::get('/commercial-business-pickup', function () {
    return view('business');
})->name('business');

Route::get('/business-pickup', function () {
    return redirect()->route('business');
});

Route::get('/commercial-pickup', function () {
    return redirect()->route('business');
});

Route::get('/free-e-waste-recycling', function () {
    return redirect()->route('ewaste');
});

Route::get('/free-e-waste', function () {
    return redirect()->route('ewaste');
});

Route::get('/ewaste', function () {
    return redirect()->route('ewaste');
});

Route::get('/contact-us', function () {
    return view('contact');
})->name('contact');

Route::get('/contact', function () {
    return redirect()->route('contact');
});

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/it-asset-management', function () {
    return view('asset-management');
})->name('asset-management');

Route::get('/asset-management', function () {
    return redirect()->route('asset-management');
});

Route::get('/it-assets-management', function () {
    return redirect()->route('asset-management');
});

Route::get('/data-destruction', function () {
    return view('data-destruction');
})->name('data-destruction');

Route::get('/data-destruction-denver', function () {
    return redirect()->route('data-destruction');
});

Route::get('/e-waste-recycling', function () {
    return view('it-disposal');
})->name('e-waste-recycling');

Route::get('/it-disposal', function () {
    return redirect()->route('e-waste-recycling');
})->name('it-disposal');

Route::get('/e-waste-it-disposal', function () {
    return redirect()->route('e-waste-recycling');
});

Route::get('/computer-liquidator-reseller', function () {
    return view('liquidator');
})->name('liquidator');

Route::get('/computer-liquidator', function () {
    return redirect()->route('liquidator');
});

Route::get('/computer-reseller', function () {
    return redirect()->route('liquidator');
});

Route::get('/charity-donation', function () {
    return view('charity');
})->name('charity');

Route::get('/charity', function () {
    return redirect()->route('charity');
});

Route::get('/electronics-donation', function () {
    return redirect()->route('charity');
});

Route::get('/community-partners', function () {
    return view('community-partners');
})->name('community-partners');

Route::get('/community', function () {
    return redirect()->route('community-partners');
});

Route::get('/community-business-partners', function () {
    return redirect()->route('community-partners');
});

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ReceivedIntakeController;
use App\Http\Controllers\User\DataDestructionController;
use App\Http\Controllers\User\ItAssetsController;
use App\Http\Controllers\User\UserOrdersController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InquiryMail;

// --- Authentication Routes (Native Customer Portal Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.page');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// --- Google OAuth Routes ---
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// --- Protected User Dashboard Routes ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Regular User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // User Received Intake Routes
    Route::controller(ReceivedIntakeController::class)->prefix('user-received-intake')->name('user.received-intake.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // User Data Destruction Routes
    Route::controller(DataDestructionController::class)->prefix('user-data-destruction')->name('user.data-destruction.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // User IT Assets Routes
    Route::controller(ItAssetsController::class)->prefix('user-it-assets')->name('user.it-assets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // User Orders Routes
    Route::controller(UserOrdersController::class)->prefix('my-orders')->name('user.orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/cancel', 'cancel')->name('cancel');
    });
});

Route::post('/contact-us', function (\Illuminate\Http\Request $request) {
    $formType = $request->input('form_type', 'general');

    if ($formType === 'pickup') {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'business_name' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'onsite_destruction' => 'nullable|string|max:50',
            'destruction_type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:3000',
        ]);
        $validated['form_type'] = 'pickup';
    } else {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:3000',
        ]);
        $validated['form_type'] = 'general';
    }

    $recipient = env('MAIL_TO_ADDRESS', env('MAIL_FROM_ADDRESS', 'info@itinvestmentrecoveries.com'));

    try {
        Mail::to($recipient)->send(new InquiryMail($validated));
    } catch (\Throwable $e) {
        Log::error('Failed to send contact inquiry email: ' . $e->getMessage(), [
            'data' => $validated,
            'exception' => $e
        ]);
    }

    $anchor = $formType === 'pickup' ? '#pickup-inquiry' : '#general-inquiry';

    return redirect()->to(url('/contact-us') . $anchor)
        ->with('success', 'Thank you for your message. It has been sent.')
        ->with('submitted_form', $formType);
})->name('contact.submit');
