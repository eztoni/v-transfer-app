<?php

use App\Http\Controllers\EditUserController;
use App\Http\Controllers\MailRenderingController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Livewire\ActivityLogDashboard;
use App\Http\Livewire\CompanyOverview;
use App\Http\Livewire\DevMailPreview;
use App\Http\Livewire\LanguageOverview;
use App\Mail\Guest\ReservationCancellationMail;
use App\Services\Api\ValamarFiskalizacija;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Models\Reservation;

/*
    |--------------------------------------------------------------------------
    | Super admin role routes
    |--------------------------------------------------------------------------
    | These routes will be available for these roles:
    |  - SUPER-ADMIN
*/


Route::get('/phpinfo', function () {return view('phpini');})->name('phpinfo');


Route::get('laravel-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('laravel-logs');
Route::get('super-admin-dashboard', [SuperAdminDashboardController::class, 'show'])->name('super-admin-dashboard');
Route::get('/language-overview', LanguageOverview::class)->name('language-overview');
Route::get('edit-user/{user}', [EditUserController::class, 'showUser'])->name('edit-user');
Route::get('/company-overview', CompanyOverview::class)->name('company-overview');
Route::get('activity-log-dashboard', ActivityLogDashboard::class)->name('activity-log-dashboard');

Route::get('/test', function () {

    $user_creation = 'Marta,Ivanlić,marta.ivanlic@valamar.com,user,valamar12345,
Martina,Šverko,martina.sverko@valamar.com,user,valamar12345,
Toni,Videkic,toni.videkic@valamar.com,user,valamar12345,
Đeni ,Bašić,deni.basic@valamar.com,user,valamar12345,
Davor,Radmilović,davor.radmilovic@valamar.com,user,valamar12345,
Žanet ,Bilić,zanet.bilic@valamar.com,user,valamar12345,
Dominik,Drndelić,dominik.drndelic@valamar.com,user,valamar12345,
Anđelika ,Ritoša,andelika.ritossa@valamar.com,admin,valamar12345,
Ozana ,Šimunović,ozana.simunovic@imperial.hr,recepcija,valamar12345,
Jasmina ,Knežić Ćumo,jasmina.kneziccumo@valamar.com,recepcija,valamar12345,
Mirko,Komnenović,mirko.komnenovic@valamar.com,recepcija,valamar12345,
Igor,Vasiljević,igor.vasiljevic@imperial.hr,recepcija,valamar12344,
Ajla ,Dizdarević,ajla.dizdarevic@valamar.com,user,valamar12345,
Aleks ,Ignjatov,aleks.ignjatov@valamar.com,user,valamar12345,
Aleksandar ,Krivokuća,aleksandar.krivokuca@valamar.com,user,valamar12345,
Alen,Rojnic,alen.rojnic@valamar.com,user,valamar12345,
Ana,Filipović Miličić,ana.filipovicmilicic@valamar.com,user,valamar12345,
Andrea,Dotto,andrea.dotto@valamar.com,user,valamar12345,
Aneli,Matić,aneli.matic@valamar.com,admin,valamar12345,
Ariana,Pribetić,ariana.pribetic@valamar.com,user,valamar12345,
Barbara,Matohanca,barbara.matohanca@valamar.com,user,valamar12345,
Branko,Juvan,branko.juvan@valamar.com,user,valamar12345,
Daniel,Koraca,daniel.koraca@valamar.com,admin,valamar12345,
David ,Botko,david.botko@valamar.com,user,valamar12345,
Davor,Radmilović,davor.radmilovic@valamar.com,user,valamar12345,
Emanuel,Zajc,emanuel.zajc@valamar.com,user,valamar12345,
Fabijan,Kadi,fabijan.kadi@valamar.com,user,valamar12345,
Fabio,Pastrovicchio,fabio.pastrovicchio@valamar.com,user,valamar12345,
Goran,Radović,goran.radovic@valamar.com,user,valamar12345,
Ira,Bosnić,ira.bosnic@valamar.com,user,valamar12345,
Iva,Pencinger Martinovic,iva.pencingermartinovic@valamar.com,user,valamar12345,
Ivan,Bobanovic,ivan.bobanovic@valamar.com,user,valamar12345,
Ivana ,Fabijančić,ivana.fabjijancic@valamar.com,user,valamar12345,
Ivana ,McMahon,ivana.mcmahon@valamar.com,admin,valamar12345,
Jelena ,Gambiroža,jelena.gambiroza@valamar.com,user,valamar12345,
Jelena ,Prekalj,jelena.prekalj@valamar.com,user,valamar12345,
Đeni,Bašić,deni.basic@valamar.com,user,valamar12345,
Juraj,Kozić,juraj.kozic@valamar.com,user,valamar12345,
Karlo,Barun,karlo.barun@valamar.com,user,valamar12345,
Klaudia,Siprak,klaudia.siprak@valamar.com,user,valamar12345,
Laura ,Soldo,laura.soldo@valamar.com,user,valamar12345,
Lena,Harnisch,lena.harnisch@valamar.com,user,valamar12345,
Lucija,Krznaric,lucija.krznaric@valamar.com,user,valamar12345,
Lucija,Kuraja,lucija.kuraja@valamar.com,user,valamar12345,
Lukrecija,Ritoša,lukrecija.ritosa@valamar.com,user,valamar12345,
Maja,Žuhović,maja.zuhovic@valamar.com,user,valamar12345,
Marko,Dogančić,marko.dogancic@valamar.com,user,valamar12345,
Marta,Grubišić,marta.grubisic@valamar.com,admin,valamar12345,
Miroslav,Ćurčija,miroslav.curcija@valamar.com,user,valamar12345,
Monika,Mikulić,monika.mikulic@valamar.com,admin,valamar12345,
Monika ,Mumelaš,monika.mumelas@valamar.com,user,valamar12345,
Nebojša,Franjić,nebojsa.franjic@valamar.com,user,valamar12345,
Nicoletta,Draghicchio,nicoletta.draghicchio@valamar.com,user,valamar12345,
Nikola,Matković,nikola.matković@valamar.com,user,valamar12345,
Nikolina,Dulčić,nikolina.dulcic@valamar.com,user,valamar12345,
Nikolina,Popić,nikolina.popic@valamar.com,user,valamar12345,
Nina ,Krmpotić,nina.krmpotic@valamar.com,user,valamar12345,
Nina ,Krulčić,nina.krulcic@valamar.com,user,valamar12345,
Nina ,Vrus,nina.vrus@valamar.com,admin,valamar12345,
Nino,Mladinić,nino.mladinic@valamar.com,user,valamar12345,
Pamela,Poldrugač,pamela.poldrugac@valamar.com,user,valamar12345,
Romina,Legović,romina.legovic@valamar.com,user,valamar12345,
Sanja,Radovčić,sanja.radovcic@valamar.com,user,valamar12345,
Sara,Kocijan,sara.kocijan@valamar.com,user,valamar12345,
Sara ,Mužina,sara.muzina@valamar.com,user,valamar12345,
Sara,Sinčić,sara.sincic@valamar.com,user,valamar12345,
Sara,Vranjanin,sara.vranjanin@valamar.com,user,valamar12345,
Stefanie,Detemple,stefaniedorothea.detemple@valamar.com,admin,valamar12345,
Tea,Pribetić,tea.pribetic@valamar.com,user,valamar12345,
Tea,Ritoša,tea.ritosa@valamar.com,user,valamar12345,
Tea,Vidulin,tea.vidulin@valamar.com,user,valamar12345,
Tomislav,Jedrejčić,tomislav.jedrejcic@valamar.com,user,valamar12345,
Toni,Videkić,toni.videkic@valamar.com,user,valamar12345,
Valentina,Došlić,valentina.doslic@valamar.com,user,valamar12345,
Valentina,Šoštarić,valentina.sostaric@valamar.com,user,valamar12345,
Vanda,Belajec,vanda.belajec@valamar.com,user,valamar12345,
Vedran,Ereiz,vedran.ereiz@valamar.com,user,valamar12345,
Vesna,Otočan,vesna.otocan@valamar.com,admin,valamar12345,
Žanet,Bilić,zanet.bilic@valamar.com,user,valamar12345,
Lacroma ,Recepcija,lacroma.recepcija@valamar.com,user,valamar12345,
President ,Recepcija,president.recepcija@valamar.com,user,valamar12345,
Argosy,Recepcija,argosy.recepcija@valamar.com,user,valamar12345,
Tirana,Recepcija,tirena.recepcija@valamar.com,user,valamar12345,
Dragan,Stanković,dragan.stankovic@imperial.hr,user,valamar12345,
Valamar Club Dubrovnik,Recepcija,vcd.recepcija@valamar.com,user,valamar12345,
Josip ,Begušić,josip.begusic@imperial.hr,user,valamar12345,
Ivona,Čamo,ivona.camo@valamar.com,user,valamar12345,
Solitudo,Recepcija,solitudo.recepcija@valamar.com,user,valamar12345,
Dalmacija ,Recepcija,dalmacija.places@imperial.hr,user,valamar12345,
Marina,Lasić,marina.lasic@imperial.hr,user,valamar12345,
Meteor,Recepcija,recepcija.meteor@imperial.hr,user,valamar12345,
Jelena ,Martić,jelena.martic@imperial.hr,user,valamar12345,
Rivijera,Recepcija,sunny.makarska@imperial.hr,user,valamar12345,
Diamant,Recepcija,recdia@valamar.com,user,valamar12345,
Ivan,Gostić,ivan.gostic@valamar.com,user,valamar12345,
Lanterna apartmani,Recepcija,reclan@valamar.com,user,valamar12345,
Davor,Korlević,davor.korlevic@valamar.com,user,valamar12345,
Solaris,Recepcija,recsol@valamar.com,user,valamar12345,
Martina ,Liović,martina.liovic@valamar.com,user,valamar12345,
Crystal,Recepcija,reckri@valamar.com,user,valamar12345,
Dragan ,Ružić,dragan.ruzic@valamar.com,user,valamar12345,
Isabella,Recepcija,valamar.isabella@valamar.com,user,valamar12345,
Igor,Bratović,igor.bratovic@valamar.com,user,valamar12345,
Riviera,Recepcija,recnep@valamar.com,user,valamar12345,
Kristina ,Teležar Dodić ,kristina.telezar@valamar.com,user,valamar12345,
Rubin,Recepcija,recrub@valamar.com,user,valamar12345,
Fran ,Volović,fran.volovic@valamar.com,user,valamar12345,
Tamaris,Recepcija,rectam@valamar.com,user,valamar12345,
Ines,Sumić,ines.sumic@valamar.com,user,valamar12345,
Istra kamp,Recepcija,recist@valamar.com,user,valamar12345,
Danijela ,Ključec Stanić,danijela.kljucec@valamar.com,user,valamar12345,
Lanterna kamp,Recepcija,recacl@valamar.com,user,valamar12345,
Danijela ,Rajko,danijela.gergeta@valamar.com,user,valamar12345,
Orsera kamp,Recepcija,recors@valamar.com,user,valamar12345,
Ivana ,Jugovac,ivana.jugovac2@valamar.com,user,valamar12345,
Marea,Recepcija,recepcija.mareasuites@valamar.com,user,valamar12345,
Tara,Redžić,tara.redzic@imperial.hr,user,valamar12345,
Parentino,Recepcija,recepcija.parentino@imperial.hr,user,valamar12345,
Nada ,Božić,nada.bozic@valamar.com,user,valamar12345,
Rabac Sunny Resort,Recepcija,recepcija.all@valamar.com,user,valamar12345,
Samanta,Luketić Peteani,samanta.luketicpeteani@valamar.com,user,valamar12345,
Sanfior,Recepcija,recepcija.san@valamar.com,user,valamar12345,
Goran ,Smoković,goran.smokovic@valamar.com,user,valamar12345,
Tunarica,Recepcija,recepcija.act@valamar.com,user,valamar12345,
Iva ,Milivoj,iva.milevoj@valamar.com,user,valamar12345,
Marina,Recepcija,recepcija.acm@valamar.com,user,valamar12345,
Leonida ,Štemberga,leonida.stemberga@valamar.com,user,valamar12345,
Bellevue,Recepcija,recepcija.bel@valamar.com,user,valamar12345,
Tamara,Šimec,tamara.simec@valamar.com,user,valamar12345,
Girandella,Recepcija,recepcija.gir@valamar.com,user,valamar12345,
Iva ,Alberti Fabeta,iva.albertifabeta@valamar.com,user,valamar12345,
Maro,Recepcija,recepcija.girandellamarosuites@valamar.com,user,valamar12345,
Sara,Modrušan Kurtić,sara.modrusankurtic@valamar.com,user,valamar12345,
Kamp Krk,Recepcija,rec.krk@valamar.com,user,valamar12345,
Vlatka,Morožin,vlatka.morozin@valamar.com,user,valamar12345,
Kamp Ježevac,Recepcija,rec.jezevac@valamar.com,user,valamar12345,
Mirela,Obradović,mirela.obradovic@valamar.com,user,valamar12345,
Kamp Škrila,Recepcija,rec.skrila@valamar.com,user,valamar12345,
Franko,Crnčić,franko.crncic@valamar.com,user,valamar12345,
Kamp Bunculuka,Recepcija,recepcija.kampbunculuka@valamar.com,user,valamar12345,
Suzane,Pavelić,suzane.pavelic@valamar.com,user,valamar12345,
Villa Adria,Recepcija,recepcija.hotelcorinthia@valamar.com,user,valamar12345,
Atrium,Recepcija,,user,valamar12345,
Corinthia,Recepcija,,user,valamar12345,
Zvonimir,Recepcija,,user,valamar12345,
Igor,Brklač,igor.brkljac@valamar.com,user,valamar12345,
Koralj,Recepcija,rec.koralj@valamar.com,user,valamar12345,
Anica ,Plentaj,anica.plentaj@valamar.com,user,valamar12345,
Baška / Zablaće,Recepcija,recepcija.kampzablace@valamar.com,user,valamar12345,
Dajana,Rakić,dajana.rakic@valamar.com,user,valamar12345,
Carolina,Recepcija,recepcija.carolina@imperial.hr,user,valamar12345,
Eva,Recepcija,,user,valamar12345,
Marko ,Pende,marko.pende@imperial.hr,user,valamar12345,
Imperial hotel,Recepcija,recepcija.imperial@imperial.hr,user,valamar12345,
Anamarija,Precca,anamarija.precca@imperial.hr,user,valamar12345,
Sanja ,Macolić,sanja.macolic@imperial.hr,user,valamar12345,
Padova hotel,Recepcija,recepcija.padova@imperial.hr,user,valamar12345,
Miljenko ,Matušan,miljenko.matusan@imperial.hr,user,valamar12345,
Padova kamp,Recepcija,padova.camping@imperial.hr,user,valamar12345,
Ines,Pahljina,ines.pahljina@imperial.hr,user,valamar12345,
San Marino kamp,Recepcija,sanmarino.camping@imperial.hr,user,valamar12345,
Petar ,Macolić,petar.macolic@imperial.hr,user,valamar12345,
San Marino resort,Recepcija,sanmarino@imperial.hr,user,valamar12345,';

    $existing_users_list = 'anabella.krizanac@valamar.com
andelika.ritossa@valamar.com
anđelika.ritossa@valamar.com
daniel.koraca@valamar.com
davor.radmilovic@valamar.com
deni.basic@valamar.com
dominik.afric@valamar.com
dominik.drndelic@valamar.com
fabijan.kadi@valamar.com
gloria.saiti@valamar.com
info@valamar-experience.com
ira.bosnic@valamar.com
ivan.gostic@valamar.com
ivan.kovacevic1996@gmail.com
ivana.fabijancic@valamar.com
jasmina.kneziccumo@valamar.com
jelena.prekalj@valamar.com
lea.heska@valamar.com
marta.ivanlic@valamar.com
martina.jurjevic@valamar.com
martina.sverko@valamar.com
matia.vukovic@valamar.com
mirko.komnenovic@valamar.com
modrictin7@gmail.com
opera@valamar.com
ozana.simunovic@imperial.hr
stefano.krizmanic@valamar.com
toni.njiric@ez-booker.com
toni.videkic@valamar.com
valamar.test@ez-booker.com
zanet.bilic@valamar.com';

    $rows = explode("\n",$user_creation);

    $users_to_add = array();

    $existing_users = explode("\n",$existing_users_list);


    $count = 11111111100;

    foreach($rows as $user_row){

        $user_data = explode(',',$user_row);

        if(!empty($user_data) & count($user_data) > 3){

            $user_name = $user_data[0];
            $user_last_name = $user_data[1];
            $user_email = strtolower($user_data[2]);
            $user_type = $user_data[3];
            if(empty($user_email)){
                continue;
            }
            if(!in_array($user_email,$existing_users)){


                $digits = 11;
                $random_oib =  rand(pow(10, $digits-1), pow(10, $digits)-1);

                $user = new \app\models\user();
                $user->name = $user_name.' '.$user_last_name;
                $user->email_verified_at = gmdate('Y-m-d h:i:s');
                $user->password = '$2y$10$Q2Y1t4nNI4PO.BzxwOeLdumpkEgNH9kWf2vdLToeWsje.h00nH7T.';
                $user->oib = $random_oib;
                $user->company_id = 1;
                $user->owner_id = 1;
                $user->destination_id = 1;
                $user->email = $user_email;

                $user->save();

                $user->availableDestinations()->sync(array(1,3,4,5,11,12,13,16));

                $role_id = 1;

                switch ($user_type){
                    case 'admin':
                        $role_id = 2;
                        break;
                    case 'recepcija':
                        $role_id = 4;
                        break;

                }

                $user->roles()->sync($role_id);


            }


        }

        $count++;
    }

});


Route::get('/dev-mail-preview', DevMailPreview::class)->name('dev-mail-preview');
Route::get('/res-mail-render/{type}/{id}', [MailRenderingController::class, 'renderReservationMail'])->name('res-mail-render');
