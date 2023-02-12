<?php


use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::middleware(
        ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN . '|' . User::ROLE_USER]
    )->group(callback: function () {
        /*
            |--------------------------------------------------------------------------
            | User routes
            |--------------------------------------------------------------------------
        */
        include_once 'role-user.php';

        Route::middleware(
            ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function () {
            /*
                |--------------------------------------------------------------------------
                | Admin routes
                |--------------------------------------------------------------------------
            */
            include_once 'role-admin.php';


            Route::middleware(
                ['role:' . User::ROLE_SUPER_ADMIN]
            )->group(function () {
                /*
                    |--------------------------------------------------------------------------
                    | SUPER  ADMIN ONLY ROUTES
                    |--------------------------------------------------------------------------
                */
                include_once 'role-super-admin.php';
            });
        });
    });
});


// For development only
if (!App::isProduction()) {
    Route::middleware(
        ['role:' . User::ROLE_SUPER_ADMIN]
    )->group(function () {
        Route::get('preview_partner_mail_list',function (){
            return  \App\Actions\Attachments\GenerateTransferOverviewPDF::generate(\Carbon\Carbon::make('2023-02-01'), now()->addDay(),\App\Models\Reservation::all())->download();


        });

    });
}
