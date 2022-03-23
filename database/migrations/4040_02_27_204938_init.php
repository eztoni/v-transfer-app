<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;


class Init extends Migration
{
    public function up()
    {

        \App\Models\Language::create(
            [
                'name' => 'English',
                'language_code' => 'en'
            ]
        );

        $company = \App\Models\Company::create(
            [
                'name' => 'My Agency',
                'contact' => 'Ez Booker',
                'email' => 'modrictin7@gmail.com',
                'country_id' => 1,
                'city' => 'Dubrovnik',
                'zip' => '20236',
            ]
        );

        \App\Models\Owner::create(
            [
                'name' =>'Valamar Rivijera',
                'company_id' => 1,
            ]
        );
        \App\Models\Owner::create(
            [
                'name' =>'Imperial Rab',
                'company_id' => 1,
            ]
        );

        $language =  \App\Models\Language::create(['name' => 'Hrvatski','language_code' => 'hr']);

        $company->languages()->attach($language);


        $role = Role::create(['name' => User::ROLE_SUPER_ADMIN]);


        $user = new User();
        $user->name = 'Ivan Kovačević';
        $user->password = Hash::make('test12345');
        $user->email = 'ivan.kovacevic1996@gmail.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->company_id = 1;
        $user->owner_id = 1;
        $user->oib = 1;
        $user->save();
        $user->assignRole('super-admin');


        $user = new User();
        $user->name = 'Toni Njirić';
        $user->password = Hash::make('test12345');
        $user->email = 'toni.njiric@ez-booker.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->company_id = 1;
        $user->owner_id = 1;
        $user->oib = 2;
        $user->save();
        $user->assignRole('super-admin');


        $user = new User();
        $user->name = 'Tin Modrić';
        $user->password = Hash::make('test12345');
        $user->email = 'modrictin7@gmail.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->company_id = 1;
        $user->owner_id = 1;
        $user->oib = 3;
        $user->save();
        $user->assignRole('super-admin');






    }

    public function down()
    {

    }
}
