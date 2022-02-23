<?php

use App\Models\AgeCategory;
use App\Models\AgeGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgeCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('age_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AgeGroup::class)->constrained();
            $table->enum('category_name', [AgeCategory::ADULT,AgeCategory::CHILD,AgeCategory::INFANT])
                ->default(AgeCategory::ADULT);
            $table->integer('age_from');
            $table->integer('age_to');
            $table->unique(['age_group_id','category_name'],'group_category_index');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('age_categories');
    }
}
