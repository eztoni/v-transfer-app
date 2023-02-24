<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePointNameInPoints extends Migration
{
    public function up()
    {
            Schema::table('points', function (Blueprint $table) {
                $table->text('name')->change();
            });

            foreach (\App\Models\Point::withoutGlobalScopes()->get() as $point){

                $original = $point->getRawOriginal('name');
                $point->setTranslation('name','en',$original);
                $point->save();
            }
    }

    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            //
        });
    }
}
