<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameFieldForRoutes extends Migration
{
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->text('name')->change();
        });

        foreach (\App\Models\Route::withoutGlobalScopes()->get() as $route){

            $original = $route->getRawOriginal('name');
            $route->setTranslation('name','en',$original);
            $route->save();
        }

    }

    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            //
        });
    }
}
