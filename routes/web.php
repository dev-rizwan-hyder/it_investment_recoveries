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

Route::get('/free-e-waste-recycling', function () {
    return view('ewaste');
})->name('ewaste');

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

Route::get('/it-disposal', function () {
    return view('it-disposal');
})->name('it-disposal');

Route::get('/e-waste-it-disposal', function () {
    return redirect()->route('it-disposal');
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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InquiryMail;

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
