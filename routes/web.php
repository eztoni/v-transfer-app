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
        ['role:' . User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN . '|' . User::ROLE_USER. '|' .User::ROLE_RECEPTION]
    )->group(callback: function () {

        /*
            |--------------------------------------------------------------------------
            | User routes
            |--------------------------------------------------------------------------
        */


       include 'role-user.php';

        Route::middleware(
            ['role:' .User::ROLE_RECEPTION .'|' .User::ROLE_SUPER_ADMIN . '|' . User::ROLE_ADMIN]
        )->group(function(){
            /*
                |--------------------------------------------------------------------------
                | Reception routes
                |--------------------------------------------------------------------------
            */
           include 'role-reception.php';
        });




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

