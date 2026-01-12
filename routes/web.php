<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MerchantController;

// Direct Payment Routes
Route::get('/', [PaymentController::class, 'showForm']);
Route::post('/pay/process', [PaymentController::class, 'processForm'])->name('pay.process');

// Payment Flow Routes
Route::get('pay/jump/{token}', [PaymentController::class, 'stealthJump'])->name('pay.jump');
Route::any('return', [PaymentController::class, 'return'])->name('pay.return');
Route::post('notify', [PaymentController::class, 'notify'])->name('pay.notify');
Route::get('pay/sync/{token}', [PaymentController::class, 'manualSync'])->name('pay.sync');

// Admin Routes (secured)
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function() {
    Route::view('/docs', 'admin.docs')->name('docs'); // Add Docs Route
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
    Route::post('/sync-supabase', [DashboardController::class, 'syncToSupabase'])->name('sync');
    Route::post('/stats/reset', [DashboardController::class, 'resetStats'])->name('stats.reset');
    
    Route::get('/payments/{order_id}/check-status', [DashboardController::class, 'checkPayHereStatus'])->name('payments.check-status');
    Route::resource('merchants', MerchantController::class);
    Route::post('merchants/{merchant}/regenerate', [MerchantController::class, 'regenerateKey'])->name('merchants.regenerate');
    
    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // Proofs Routes
    Route::get('/proofs', [\App\Http\Controllers\Admin\ProofController::class, 'index'])->name('proofs.index');
    Route::get('/proofs/invoice/{payment}', [\App\Http\Controllers\Admin\ProofController::class, 'invoice'])->name('proofs.invoice');
    Route::post('/proofs/invoice/{payment}/download', [\App\Http\Controllers\Admin\ProofController::class, 'downloadInvoice'])->name('proofs.invoice.download');
    Route::get('/proofs/email/{payment}', [\App\Http\Controllers\Admin\ProofController::class, 'email'])->name('proofs.email');
});
