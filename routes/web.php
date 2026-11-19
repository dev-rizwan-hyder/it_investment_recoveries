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

Route::get('/charity-donations', function () {
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
use App\Http\Controllers\User\UniversalWasteController;
use App\Http\Controllers\User\EsgReportController;
use App\Http\Controllers\User\RepairOrderController;
use App\Http\Controllers\User\UserOrdersController;
use App\Http\Controllers\User\UserInvoiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\TimeTrackingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RecyclingIntakeController;
use App\Http\Controllers\Admin\ProcessingIntakeController;
use App\Http\Controllers\Admin\EcommerceEntriesController;
use App\Http\Controllers\Admin\RefurbishingEntriesController;
use App\Http\Controllers\Admin\UniversalWasteEntriesController;
use App\Http\Controllers\Admin\DataDestructionEntriesController;
use App\Http\Controllers\Admin\ItAssetsEntriesController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\ItemIntakeController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\SalesInvoiceController;
use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EbayProductController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EbaySummaryController;
use App\Http\Controllers\EbayController;
use App\Http\Controllers\EbayWebhookController;
use App\Http\Controllers\EbayCallbackController;
use App\Http\Controllers\SquareWebhookController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InquiryMail;

// --- Authentication Routes (Role-Based Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/admin/login', fn () => redirect()->route('login'))->name('admin.login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.page');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// --- Google OAuth Routes ---
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Hidden public lead manager. Intentionally unlinked and outside auth middleware.
Route::prefix('/view/personal/summary/lead')->name('summary.leads.')->group(function () {
    Route::get('/', [LeadController::class, 'index'])->name('index');
    Route::get('/create', [LeadController::class, 'create'])->name('create');
    Route::post('/', [LeadController::class, 'store'])->name('store');
    Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
    Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
    Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
    Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
});

Route::prefix('/view/ebay/summary')->name('summary.ebay.')->group(function () {
    Route::get('/', [EbaySummaryController::class, 'index'])->name('index');
    Route::post('/', [EbaySummaryController::class, 'store'])->name('store');
    Route::post('/image-search', [EbaySummaryController::class, 'imageSearch'])->name('image-search');
    Route::post('/remove-bg', [EbaySummaryController::class, 'removeBackground'])->name('remove-bg');
    Route::post('/{folder}/images', [EbaySummaryController::class, 'uploadImages'])->name('images.store');
    Route::delete('/{folder}/images/{filename}/delete', [EbaySummaryController::class, 'destroyImage'])->name('images.destroy');
    Route::patch('/{folder}/mark', [EbaySummaryController::class, 'toggleMark'])->name('toggle-mark');
    Route::patch('/{folder}', [EbaySummaryController::class, 'update'])->name('update');
    Route::delete('/{folder}', [EbaySummaryController::class, 'destroy'])->name('destroy');
});

Route::get('/ebay/products-detail-hidden', [\App\Http\Controllers\Admin\EbayProductController::class, 'hiddenDetail'])->name('ebay.hidden-products');
Route::get('/view/ebay/products-detail-hidden', [\App\Http\Controllers\Admin\EbayProductController::class, 'hiddenDetail']);

Route::get('/ebay/products', [EbayController::class, 'products'])->name('ebay.products');
Route::get('/ebay/browse', [EbayController::class, 'browse'])->name('ebay.browse');
Route::get('/ebay/login', [EbayController::class, 'login']);
Route::get('/ebay/callback', [EbayController::class, 'callback']);
Route::get('/ebay/cancel', [EbayController::class, 'cancel']);
Route::get('/ebay/orders/status', [EbayController::class, 'ordersStatus'])->name('ebay.orders.status');
Route::match(['get', 'post'], '/ebay/orders/sync', [EbayController::class, 'syncOrders'])->name('ebay.orders.sync');
Route::view('/privacy-policy', 'privacy');

Route::get('/api/ebay/callback', [EbayCallbackController::class, 'handle']);
Route::post('/api/ebay/callback', [EbayCallbackController::class, 'handle']);

Route::get('/api/ebay/account-deletion', [EbayWebhookController::class, 'challenge']);
Route::post('/api/ebay/account-deletion', [EbayWebhookController::class, 'notification']);
Route::get('/ebay/account-deletion', [EbayWebhookController::class, 'challenge']);
Route::post('/ebay/account-deletion', [EbayWebhookController::class, 'notification']);

Route::post('/api/square/webhook', [\App\Http\Controllers\SquareWebhookController::class, 'handle'])->name('square.webhook');


// --- Protected User Dashboard Routes ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Regular User Dashboard
    // Dashboard route
    Route::get('/dashboard', function () {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return app(UserDashboardController::class)->index();
    })->name('dashboard');

    // User Received Intake Routes
    Route::controller(ReceivedIntakeController::class)->prefix('user-received-intake')->name('user.received-intake.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // User Data Destruction Routes
    Route::controller(DataDestructionController::class)->prefix('user-data-destruction')->name('user.data-destruction.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // User Universal Waste & Certificates Routes
    Route::controller(UniversalWasteController::class)->prefix('user-universal-waste')->name('user.universal-waste.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // User ESG Sustainability Report Routes
    Route::controller(EsgReportController::class)->prefix('user-esg-report')->name('user.esg-report.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // User Repair Orders Routes
    Route::controller(RepairOrderController::class)->prefix('user-repair-orders')->name('user.repair-orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // User IT Assets Routes
    Route::controller(ItAssetsController::class)->prefix('user-it-assets')->name('user.it-assets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/bulk-delete', 'bulkDestroy')->name('bulk-delete');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // User Orders Routes
    Route::controller(UserOrdersController::class)->prefix('my-orders')->name('user.orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/cancel', 'cancel')->name('cancel');
    });

    // User Invoices (Paid / Unpaid) Routes
    Route::controller(UserInvoiceController::class)->prefix('user-invoices')->name('user.invoices.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/print', 'printInvoice')->name('print');
        Route::post('/{id}/pay-square', 'paySquare')->name('pay-square');
        Route::post('/{id}/sync-square', 'syncSquare')->name('sync-square');
    });
    // --- Admin Only Routes ---
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

        // Main Dashboards
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/sales', [DashboardController::class, 'sales'])->name('sales');

        // Intake Section - Recycling
        Route::controller(App\Http\Controllers\Admin\RecyclingIntakeController::class)->prefix('recycling-intake')->name('recycling.intake')->group(function () {
            Route::get('/', 'index');
            Route::get('get-barcode', 'getBarcode')->name('.barcode');
            Route::get('get-barcode-image', 'getBarcodeImage')->name('.barcode-image');
            Route::get('get-job-number', 'getJobNumber')->name('.job-number');
            Route::get('get-clients', 'getClientsList')->name('.clients');
            Route::post('calculate-net', 'calculateNet')->name('.calculate-net');
            Route::post('create-client', 'createClient')->name('.create-client');
            Route::post('save-quick', 'saveQuick')->name('.save-quick');
            Route::post('save-full', 'saveFull')->name('.save-full');
        });

        // --- FIXED: Processing Intake ---
        Route::controller(ProcessingIntakeController::class)
            ->prefix('processing-intake')
            ->name('processing-intake')
            ->group(function () {
                Route::get('/', 'index')->name('.index');
                Route::get('/stats', 'getStats')->name('.stats');
                Route::get('/get-pallets', 'getPallets')->name('.get-pallets');
                Route::post('/bulk-status', 'bulkUpdateStatus')->name('.bulk-status');
                Route::delete('/bulk-delete', 'bulkDelete')->name('.bulk-delete');
                Route::post('/bulk-send-email', 'bulkSendEmail')->name('.bulk-send-email');

                // This creates: /admin/processing-intake/{id}/details
                Route::get('/{id}/details', 'getPalletDetails')->name('.get-pallet-details');

                Route::post('/{id}/update-status', 'updateStatus')->name('.update-status');
                Route::post('/{id}/update', 'updatePallet')->name('.update-pallet');
                Route::delete('/{id}/delete', 'deletePallet')->name('.delete-pallet');
                Route::get('/export-csv', 'exportCsv')->name('.export-csv');
                Route::get('/clients', 'getClients')->name('.get-clients');
                Route::get('/marketing-refs', 'getMarketingRefs')->name('.get-marketing-refs');
            });


        // Item Intake - AJAX Based Controller
        Route::controller(ItemIntakeController::class)->prefix('items-lot')->name('items-lot.')->group(function () {
            Route::get('/intake', 'index')->name('intake');
            Route::get('/generate-barcode', 'generateBarcode')->name('generate-barcode');
            Route::get('/generate-existing-barcode', 'generateExistingBarcode')->name('generate-existing-barcode');
            Route::get('/categories', 'getCategories')->name('categories');
            Route::get('/sub-categories', 'getSubCategories')->name('sub-categories');
            Route::get('/pallets', 'getPallets')->name('pallets');
            Route::get('/pallet/{id}/details', 'getPalletDetails')->name('pallet-details');
            Route::get('/get-job-number', 'getJobNumber')->name('get-job-number');
            Route::post('/create-client', 'createClient')->name('create-client');
            Route::post('/save', 'saveItem')->name('save');
        });

        // Ecommerce Entries - AJAX Based Controller
        Route::controller(EcommerceEntriesController::class)->prefix('ecommerce-entries')->name('ecommerce-entries.')->group(function () {
            // Main page
            Route::get('/', 'index')->name('index');

            // Item operations
            Route::get('/{itemId}/details', 'showItemDetails')->name('details');
            Route::get('/{itemId}/edit', 'loadEditModal')->name('edit-modal');
            Route::post('/{itemId}', 'update')->name('update');
            Route::put('/{itemId}/quantity', 'updateQuantity')->name('update-quantity');
            Route::put('/{itemId}/status', 'updateStatus')->name('update-status');
            Route::put('/bulk-status', 'bulkUpdateStatus')->name('bulk-status');
            Route::delete('/{itemId}/photo', 'deletePhoto')->name('delete-photo');
            Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::delete('/{itemId}', 'delete')->name('delete');

            // Inventory transfer
            Route::get('/{itemId}/prepare-inventory', 'prepareToInventory')->name('prepare-inventory');
            Route::get('/generate-barcode', 'generateInventoryBarcode')->name('generate-barcode');
            Route::post('/{itemId}/save-inventory', 'saveToInventory')->name('save-inventory');

            // Export
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
        });

        Route::get('refurbishing', [RefurbishingEntriesController::class, 'index'])->name('refurbishing-entries.index');
        // AJAX list (GET with query params)
        Route::get('refurbishing/list', [RefurbishingEntriesController::class, 'index'])->name('refurbishing-entries.list');
        // Export CSV
        Route::get('refurbishing/export-csv', [RefurbishingEntriesController::class, 'exportCsv'])->name('refurbishing-entries.export-csv');
        // Single item details
        Route::get('refurbishing/{itemId}/details', [RefurbishingEntriesController::class, 'showItemDetails'])->name('refurbishing-entries.details');
        // Edit modal data
        Route::get('refurbishing/{itemId}/edit', [RefurbishingEntriesController::class, 'loadEditModal'])->name('refurbishing-entries.edit-modal');
        // Update full item  (POST with FormData)
        Route::post('refurbishing/{itemId}', [RefurbishingEntriesController::class, 'update'])->name('refurbishing-entries.update');
        // Bulk status
        Route::put('refurbishing/bulk-status', [RefurbishingEntriesController::class, 'bulkUpdateStatus'])->name('refurbishing-entries.bulk-status');
        // Bulk delete
        Route::delete('refurbishing/bulk-delete', [RefurbishingEntriesController::class, 'bulkDelete'])->name('refurbishing-entries.bulk-delete');
        // Inline status
        Route::put('refurbishing/{itemId}/update-status', [RefurbishingEntriesController::class, 'updateStatus'])->name('refurbishing-entries.update-status');
        // Inline quantity
        Route::put('refurbishing/{itemId}/update-quantity', [RefurbishingEntriesController::class, 'updateQuantity'])->name('refurbishing-entries.update-quantity');
        // Delete photo
        Route::delete('refurbishing/{itemId}/photo', [RefurbishingEntriesController::class, 'deletePhoto'])->name('refurbishing-entries.delete-photo');
        // Delete item
        Route::delete('refurbishing/{itemId}', [RefurbishingEntriesController::class, 'delete'])->name('refurbishing-entries.delete');
        // Prepare inventory data (GET)
        Route::get('refurbishing/{itemId}/prepare-inventory', [RefurbishingEntriesController::class, 'prepareToInventory'])->name('refurbishing-entries.prepare-inventory');
        // Generate barcode for inventory (GET)
        Route::get('refurbishing/generate-barcode', [RefurbishingEntriesController::class, 'generateInventoryBarcode'])->name('refurbishing-entries.generate-barcode');
        // Save to inventory (POST)
        Route::post('refurbishing/{itemId}/save-inventory', [RefurbishingEntriesController::class, 'saveToInventory'])->name('refurbishing-entries.save-inventory');
        // Shippo rates (POST)
        Route::post('refurbishing/shippo/rates', [RefurbishingEntriesController::class, 'getShippoRates'])->name('refurbishing-entries.shippo-rates');

        // Universal Waste Entries (New MVC-based routes)
        Route::controller(UniversalWasteEntriesController::class)->prefix('universal-waste-entries')->name('universal-waste-entries.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/load-data', 'loadData')->name('load-data');
            Route::get('/stats', 'getStats')->name('stats');
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
            Route::get('/{id}', 'showItemDetails')->name('details');
            Route::get('/{id}/edit', 'loadEditModal')->name('edit-modal');
            Route::post('/{id}', 'update')->name('update');
            Route::put('/bulk-status', 'bulkUpdateStatus')->name('bulk-status');
            Route::post('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/quantity', 'updateQuantity')->name('update-quantity');
            Route::post('/{id}/delete-photo', 'deletePhoto')->name('delete-photo');
            Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::delete('/{id}', 'delete')->name('delete');
            Route::get('/{id}/prepare-inventory', 'prepareToInventory')->name('prepare-inventory');
            Route::get('/generate-barcode', 'generateInventoryBarcode')->name('generate-barcode');
            Route::post('/{id}/save-inventory', 'saveToInventory')->name('save-inventory');
        });

        // Data Destruction Entries
        Route::controller(DataDestructionEntriesController::class)->prefix('data-destruction-entries')->name('data-destruction-entries.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/load-data', 'loadData')->name('load-data');
            Route::get('/stats', 'getStats')->name('stats');
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
            Route::get('/{id}', 'showItemDetails')->name('details');
            Route::get('/{id}/edit', 'loadEditModal')->name('edit-modal');
            Route::post('/{id}', 'update')->name('update');
            Route::put('/bulk-status', 'bulkUpdateStatus')->name('bulk-status');
            Route::post('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/quantity', 'updateQuantity')->name('update-quantity');
            Route::post('/{id}/delete-photo', 'deletePhoto')->name('delete-photo');
            Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::delete('/{id}', 'delete')->name('delete');
            Route::get('/{id}/prepare-inventory', 'prepareToInventory')->name('prepare-inventory');
            Route::get('/generate-barcode', 'generateInventoryBarcode')->name('generate-barcode');
            Route::post('/{id}/save-inventory', 'saveToInventory')->name('save-inventory');
        });

        // IT Assets Entries
        Route::controller(\App\Http\Controllers\Admin\ItAssetsEntriesController::class)->prefix('it-assets-entries')->name('it-assets-entries.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/load-data', 'loadData')->name('load-data');
            Route::get('/stats', 'getStats')->name('stats');
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
            Route::get('/{id}', 'showItemDetails')->name('details');
            Route::get('/{id}/edit', 'loadEditModal')->name('edit-modal');
            Route::post('/{id}', 'update')->name('update');
            Route::put('/bulk-status', 'bulkUpdateStatus')->name('bulk-status');
            Route::post('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/quantity', 'updateQuantity')->name('update-quantity');
            Route::post('/{id}/delete-photo', 'deletePhoto')->name('delete-photo');
            Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::delete('/{id}', 'delete')->name('delete');
            Route::get('/{id}/prepare-inventory', 'prepareToInventory')->name('prepare-inventory');
            Route::get('/generate-barcode', 'generateInventoryBarcode')->name('generate-barcode');
            Route::post('/{id}/save-inventory', 'saveToInventory')->name('save-inventory');
        });

        // Inventory Management
        Route::controller(InventoryController::class)->prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/load-data', 'loadData')->name('load-data');
            Route::get('/stats', 'getStats')->name('stats');
            Route::get('/categories-tree', 'getCategoriesTree')->name('categories-tree');
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
            Route::post('/remove-bg', 'removeBackground')->name('remove-bg');
            Route::post('/fetch-ebay', 'fetchFromEbay')->name('fetch-ebay');
            Route::get('/ebay-listings', 'getEbayListings')->name('ebay-listings');
            Route::post('/sync-single-ebay', 'syncSingleEbayItem')->name('sync-single-ebay');
            Route::post('/match-ebay-quantities', 'matchSyncedEbayQuantities')->name('match-ebay-quantities');
            Route::post('/delete-all-ebay', 'deleteAllEbay')->name('delete-all-ebay');
            Route::post('/sync-ebay-orders', 'syncEbayOrders')->name('sync-ebay-orders');
            Route::post('/{id}/direct-sell', 'directSell')->name('direct-sell');
            Route::get('/{id}/edit', 'loadEditModal')->name('edit-modal');
            Route::get('/{id}/shipping-rates', 'getShippingRates')->name('shipping-rates');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::post('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/delete-photo', 'deletePhoto')->name('delete-photo');
            Route::post('/{id}/sync', 'syncToStore')->name('sync-to-store');
            Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::delete('/{id}', 'delete')->name('delete');
        });

        // Category Management
        Route::controller(CategoryManagementController::class)->prefix('categories')->name('categories.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Employee Management
        Route::get('employees/{employee}/profile-report', [EmployeesController::class, 'profileReport'])->name('employees.profile-report');
        Route::resource('employees', EmployeesController::class);

        // Attendance Management
        Route::controller(AttendanceController::class)->prefix('attendance')->group(function () {
            Route::get('/', 'index')->name('attendance'); // This creates 'admin.attendance'
            Route::get('/create', 'create')->name('attendance.create');
            Route::post('/store', 'store')->name('attendance.store');
            Route::get('/report-pdf', 'reportPdf')->name('attendances.report_pdf');
            Route::get('/get-status', 'getStatus')->name('attendance.get_status');
        });

        Route::resource('policies', PolicyController::class);
        Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class);

        Route::get('/time-tracking', [TimeTrackingController::class, 'index'])->name('time-tracking');
        Route::post('/time-tracking/store', [TimeTrackingController::class, 'store'])->name('time-tracking.store');
        Route::get('/time-tracking/assign', [TimeTrackingController::class, 'assignView'])->name('time-tracking.assign');
        Route::post('/time-tracking/assign/save', [TimeTrackingController::class, 'saveAssignment'])->name('time-tracking.save-assignment');
        Route::put('/time-tracking/update/{id}', [TimeTrackingController::class, 'update'])->name('time-tracking.update');
        Route::delete('/time-tracking/delete/{id}', [TimeTrackingController::class, 'destroy'])->name('time-tracking.destroy');
        // Repair Intake Entries
        Route::get('/repair-intake', [RepairController::class, 'index'])->name('repair-intake.index');
        Route::get('/repair-entries', fn() => redirect()->route('admin.repair-intake.index'))->name('repair.entries');
        Route::post('/repair-entries', [RepairController::class, 'store'])->name('repair.entries.store');
        Route::get('/repair-intake/export', [RepairController::class, 'exportCsv'])->name('repair.export');
        Route::get('/repair-entries/generate-barcode', [RepairController::class, 'generateBarcode'])->name('repair.entries.generate-barcode');
        Route::patch('/repair-entries/{id}/status', [RepairController::class, 'updateStatus'])->name('repair.entries.update-status');
        Route::patch('/repair-entries/{id}/quantity', [RepairController::class, 'updateQuantity'])->name('repair.entries.update-quantity');
        Route::put('/repair-entries/{id}/payment-method', [RepairController::class, 'updatePaymentMethod'])->name('repair.entries.update-payment-method');
        Route::put('/repair-entries/{id}/payment-status', [RepairController::class, 'updatePaymentStatus'])->name('repair.entries.update-payment-status');
        Route::get('/repair-entries/{id}/invoice-print', [RepairController::class, 'printInvoice'])->name('repair.entries.invoice-print');
        Route::get('/repair-entries/{id}', [RepairController::class, 'show'])->name('repair.entries.show');
        Route::get('/repair-entries/{id}/edit', [RepairController::class, 'edit'])->name('repair.entries.edit');
        Route::put('/repair-entries/{id}', [RepairController::class, 'update'])->name('repair.entries.update');
        Route::delete('/repair-entries/{id}', [RepairController::class, 'destroy'])->name('repair.entries.destroy');

        // Orders
        Route::get('/orders/export', [OrdersController::class, 'exportCsv'])->name('orders.export');
        Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
        Route::put('/orders/bulk-status', [OrdersController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');
        Route::delete('/orders/bulk-delete', [OrdersController::class, 'bulkDestroy'])->name('orders.bulk-delete');
        Route::get('/orders/{id}/receipt-preview', [OrdersController::class, 'receiptPreview'])->name('orders.receipt.preview');
        Route::get('/orders/{id}/receipt', [OrdersController::class, 'receipt'])->name('orders.receipt');
        Route::get('/orders/{id}', [OrdersController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}/payment-method', [OrdersController::class, 'updatePaymentMethod'])->name('orders.update-payment-method');
        Route::put('/orders/{id}/payment-status', [OrdersController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
        Route::put('/orders/{id}/status', [OrdersController::class, 'updateStatus'])->name('orders.update-status');
        Route::delete('/orders/{id}', [OrdersController::class, 'destroy'])->name('orders.destroy');
        Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
        Route::get('attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // Sales & Finance
        Route::get('/clients', [ClientController::class, 'index'])->name('clients');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::delete('/clients/bulk-delete', [ClientController::class, 'bulkDelete'])->name('clients.bulk-delete');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::get('/sales/invoices', [SalesInvoiceController::class, 'index'])->name('sales.invoices');
        Route::get('/sales/invoices/create', [SalesInvoiceController::class, 'create'])->name('sales.invoices.create');
        Route::post('/sales/invoices', [SalesInvoiceController::class, 'store'])->name('sales.invoices.store');
        Route::delete('/sales/invoices/bulk-delete', [SalesInvoiceController::class, 'bulkDestroy'])->name('sales.invoices.bulk-delete');
        Route::get('/sales/invoices/{invoice}', [SalesInvoiceController::class, 'show'])->name('sales.invoices.show');
        Route::get('/sales/invoices/{invoice}/edit', [SalesInvoiceController::class, 'edit'])->name('sales.invoices.edit');
        Route::put('/sales/invoices/{invoice}', [SalesInvoiceController::class, 'update'])->name('sales.invoices.update');
        Route::get('/sales/invoices/{invoice}/print', [SalesInvoiceController::class, 'printInvoice'])->name('sales.invoices.print');
        Route::post('/sales/invoices/{invoice}/send-square', [SalesInvoiceController::class, 'sendSquare'])->name('sales.invoices.send-square');
        Route::post('/sales/invoices/{invoice}/sync-square', [SalesInvoiceController::class, 'syncSquare'])->name('sales.invoices.sync-square');
        Route::post('/sales/invoices/{invoice}/cancel-square', [SalesInvoiceController::class, 'cancelSquare'])->name('sales.invoices.cancel-square');
        Route::post('/sales/invoices/{invoice}/remind-square', [SalesInvoiceController::class, 'remindSquare'])->name('sales.invoices.remind-square');
        Route::post('/sales/invoices/{invoice}/send-email', [SalesInvoiceController::class, 'sendEmail'])->name('sales.invoices.send-email');
        Route::delete('/sales/invoices/{invoice}', [SalesInvoiceController::class, 'destroy'])->name('sales.invoices.destroy');
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses');
        Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        // Report Generate
        Route::get('/reports', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/fetch-items', [ReportController::class, 'fetchItems'])->name('report.fetchItems');
        Route::get('/report/debug', [ReportController::class, 'debugData'])->name('report.debug');
        Route::get('/report/preview', [ReportController::class, 'preview'])->name('report.preview');
        Route::post('/report/store', [ReportController::class, 'store'])->name('report.store');
        Route::post('/report/send-email', [ReportController::class, 'sendEmail'])->name('report.send-email');

        // Ebay Products
        Route::controller(\App\Http\Controllers\Admin\EbayProductController::class)->prefix('ebay-products')->name('ebay-products.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::post('/temp-upload', 'tempUpload')->name('temp-upload');
            Route::post('/image-search', 'imageSearch')->name('image-search');
            Route::patch('/{folder}', 'update')->name('update');
            Route::patch('/{folder}/toggle-mark', 'toggleMark')->name('toggle-mark');
            Route::match(['post', 'patch'], '/{folder}/toggle-blink', 'toggleBlink')->name('toggle-blink');
            Route::post('/{folder}/mark-sold', 'markAsSold')->name('mark-sold');
            Route::delete('/{folder}/inventory', 'destroyInventory')->name('inventory.destroy');
            Route::delete('/{folder}', 'destroy')->name('destroy');
            Route::delete('/{folder}/images/{filename}', 'deleteImage')->name('images.destroy');
            Route::post('/{folder}/images', 'uploadImages')->name('images.store');
            Route::get('/{folder}/prepare-inventory', 'prepareToInventory')->name('prepare-inventory');
            Route::post('/{folder}/save-inventory', 'saveToInventory')->name('save-inventory');
        });

        // System Settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');

        // Trash Management
        Route::controller(TrashController::class)->prefix('trash')->name('trash.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'getData')->name('data');
            Route::post('/{section}/{id}/restore', 'restore')->name('restore');
            Route::delete('/{section}/{id}/force-delete', 'forceDelete')->name('force-delete');
            Route::post('/bulk-restore', 'bulkRestore')->name('bulk-restore');
            Route::delete('/bulk-force-delete', 'bulkForceDelete')->name('bulk-force-delete');
            Route::delete('/{section}/empty', 'emptyTrash')->name('empty');
        });
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
