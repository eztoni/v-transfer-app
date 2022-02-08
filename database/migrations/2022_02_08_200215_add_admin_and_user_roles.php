<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class AddAdminAndUserRoles extends Migration
{
    public function up()
    {
        Role::create(['name' => User::ROLE_USER]);
        Role::create(['name' => User::ROLE_ADMIN]);

    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            //
        });
    }
}
