<?php

use App\Http\Controllers\EditUserController;
use App\Http\Controllers\MailRenderingController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Livewire\ActivityLogDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\DevMailPreview;
use App\Http\Livewire\LanguageOverview;
use App\Mail\Guest\ReservationCancellationMail;
use App\Services\Api\ValamarFiskalizacija;
use Illuminate\Support\Facades\Mail;

/*
    |--------------------------------------------------------------------------
    | Super admin role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |  - SUPER-ADMIN
*/


Route::get('/phpinfo', function () {return view('phpini');})->name('phpinfo');


Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
Route::get('/language-overview', LanguageOverview::class)->name('language-overview');
Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
Route::get('activity-log-dashboard', ActivityLogDashboard::class)->name('activity-log-dashboard');

Route::get('/test', function () {

    $mail = new ReservationCancellationMail(94,$locale??'en');
    $userEmails = array('njiric.toni@gmail.com');

    Mail::to($userEmails)->locale($locale??'en')->send($mail);

});


Route::get('/dev-mail-preview', DevMailPreview::class)->name('dev-mail-preview');
Route::get('/res-mail-render/{type}/{id}', [MailRenderingController::class, 'renderReservationMail'])->name('res-mail-render');
