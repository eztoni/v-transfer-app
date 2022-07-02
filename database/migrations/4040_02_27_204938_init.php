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

        \App\Models\Company::create(
            [
                'name' => 'Valamar',
                'contact' => 'Valamar',
                'email' => 'Valamar',
                'country_id' => 1,
                'city' => 'Dubrovnik',
                'zip' => '20236',
            ]
        );

        \App\Models\Owner::create(
            [
                'name' => 'Valamar Rivijera',
                'company_id' => 1,
            ]
        );
        \App\Models\Owner::create(
            [
                'name' => 'Imperial Rab',
                'company_id' => 1,
            ]
        );

        $destinations = [
            [
                'name' => 'Dubrovnik'
            ],
            [
                'name' => 'Makarska'
            ],
            [
                'name' => 'Poreč'
            ],
            [
                'name' => 'Rabac'
            ],
            [
                'name' => 'Krk'
            ],
            [
                'name' => 'Rab'
            ],
            [
                'name' => 'Hvar'
            ],
            [
                'name' => 'Obertauern'
            ],

        ];

        $owner = \App\Models\Owner::find(1);
        foreach ($destinations as $destinationData){
            $destination = new \App\Models\Destination();
            $destination->owner_id = 1;
            $destination->fill($destinationData);
            $destination->save();
            $owner->destinations()->save(($destination));
        }



        \App\Models\Language::create(['name' => 'Hrvatski', 'language_code' => 'hr']);


        Role::create(['name' => User::ROLE_SUPER_ADMIN]);


        $user = new User();
        $user->name = 'Ivan Kovačević';
        $user->password = Hash::make('test12345');
        $user->email = 'ivan.kovacevic1996@gmail.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->company_id = 1;
        $user->owner_id = 1;
        $user->destination_id = 1;
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
        $user->destination_id = 1;

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
        $user->destination_id = 1;

        $user->oib = 3;
        $user->save();
        $user->assignRole('super-admin');

        $user = new User();
        $user->name = 'Valamar Test';
        $user->password = Hash::make('&5T%4$5a#2lK');
        $user->email = 'valamar.test@ez-booker.com';
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->company_id = 1;
        $user->owner_id = 1;
        $user->destination_id = 1;

        $user->oib = 4;
        $user->save();
        $user->assignRole(User::ROLE_ADMIN);


        Storage::disk('public')->deleteDirectory('/images');


    }

    public function down()
    {

    }
}
