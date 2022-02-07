<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class CreateSuperadmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::create(['name' => 'super-admin']);


        $user = new User();
        $user->name = 'Ivan Kovačević';
        $user->password = Hash::make('test12345');
        $user->email = 'ivan.kovacevic1996@gmail.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();
        $user->assignRole('super-admin');


        $user = new User();
        $user->name = 'Toni Njirić';
        $user->password = Hash::make('test12345');
        $user->email = 'toni.njiric@ez-booker.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();
        $user->assignRole('super-admin');


        $user = new User();
        $user->name = 'Tin Modrić';
        $user->password = Hash::make('test12345');
        $user->email = 'modrictin7@gmail.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();
        $user->assignRole('super-admin');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('superadmins');
    }
}
