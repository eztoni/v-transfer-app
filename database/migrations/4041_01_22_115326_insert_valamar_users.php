<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertValamarUsers extends Migration
{
    public function up()
    {
        $users = array(
            array(
                "full_name" => "Abijan Kadi",
                "email" => "Fabijan.kadi@valamar.com",
                "role" => "user"
            ),
            array(
                "full_name" => "Martina Jurjevic",
                "email" => "Martina.jurjevic@valamar.com",
                "role" => "user"
            ),
            array(
                "full_name" => "Ivan Gostić",
                "email" => "ivan.gostic@valamar.com",
                "role" => "user"
            ),
            array(
                "full_name" => "Toni Videkić",
                "email" => "Toni.videkic@valamar.com",
                "role" => "user"
            ),
            array(
                "full_name" => "Jelena Prekalj",
                "email" => "Jelena.prekalj@valamar.com",
                "role" => "user"
            ),
            array(
                "full_name" => "Daniel Koraca",
                "email" => "Daniel.koraca@valamar.com",
                "role" => "admin"
            ),
            array(
                "full_name" => "Matia Vuković Flego",
                "email" => "Matia.vukovic@valamar.com",
                "role" => "admin"
            )
        );


        $i = 0;
        foreach ($users as $user){

            $um = new \App\Models\User(
                [
                    'name' => $user['full_name'],
                    'email' => $user['email']
                ]
            );

            $um->password = Hash::make('valamar12345');
            $i++;
            $um->oib = '000000000000'. $i;
            $um->email_verified_at = now();

            $um->company_id = \App\Models\Company::first()->id;
            $um->owner_id = \App\Models\Owner::first()->id;
            $um->destination_id = \App\Models\Destination::withoutGlobalScopes()->first()->id;

            $um->assignRole($user['role']);

            $um->save();

            $um->availableDestinations()->sync(\App\Models\Destination::pluck('id'));

        }



    }

    public function down()
    {

       $users =  \App\Models\User::whereIn('email',[
             "Fabijan.kadi@valamar.com",
             "Martina.jurjevic@valamar.com",
             "ivan.gostic@valamar.com",
             "Toni.videkic@valamar.com",
             "Jelena.prekalj@valamar.com",
             "Daniel.koraca@valamar.com",
             "Matia.vukovic@valamar.com",
        ])->get();

       foreach ($users as $user){
           $user->availableDestinations()->sync([]);
           $user->delete();
       }



    }
}
