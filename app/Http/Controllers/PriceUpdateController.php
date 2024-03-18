<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationAlertEvent;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarOperaApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use DB;

class PriceUpdateController extends Controller
{

    private $update_destinations = array(
        12,
        1
    );

    function __construct()
    {

    }

    public function update()
    {

        $cijene = 'Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Čilipi,58,116,RPO,20,6215,12,14,8,34
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Čilipi - Dubrovnik ,58,116,RPO,20,6216,12,14,8,35
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Ston / Orebić,280,560,RPO,20,6219,12,14,8,38
Dubrovnik,Putnik d.o.o.,Car transfer,Ston / Orebić - Dubrovnik,280,560,RPO,20,6220,12,14,8,39
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Mostar,380,760,RPO,20,6223,12,14,8,40
Dubrovnik,Putnik d.o.o.,Car transfer,Mostar -  Dubrovnik,380,760,RPO,20,6224,12,14,8,41
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Split,440,880,RPO,20,6227,12,14,8,42
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Split -  Dubrovnik,440,880,RPO,20,6228,12,14,8,43
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Makarska,360,720,RPO,20,6231,12,14,8,44
Dubrovnik,Putnik d.o.o.,Car transfer,Makarska  - Dubrovnik,360,720,RPO,20,6232,12,14,8,45
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Čilipi (24H),67,134,RPO,20,6235,12,14,8,46
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Čilipi - Dubrovnik  (24H),67,134,RPO,20,6236,12,14,8,47
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Čilipi - Dubrovnik (Included in price) Lacroma,55,110,PPOM,10,6240,12,14,8,48
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Čilipi (Included in price) Lacroma,55,110,PPOM,10,6239,12,14,8,49
Dubrovnik,Putnik d.o.o.,Car transfer,"Airport Čilipi -  Dubrovnik (Included in price) Tirena, VCD",42,84,PPOM,10,6240,12,14,8,50
Dubrovnik,Putnik d.o.o.,Car transfer,"Dubrovnik - Airport Ćilipi (Included in price) Tirena, VCD",42,84,PPOM,10,6239,12,14,8,78
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Airport Čilipi,76,152,RPO,20,6217,12,15,8,34
Dubrovnik,Putnik d.o.o.,Minivan transfer,Airport Čilipi - Dubrovnik ,76,152,RPO,20,6218,12,15,8,35
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Ston / Orebić,340,680,RPO,20,6221,12,15,8,38
Dubrovnik,Putnik d.o.o.,Minivan transfer,Ston / Orebić - Dubrovnik,340,680,RPO,20,6222,12,15,8,39
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Mostar,440,880,RPO,20,6225,12,15,8,40
Dubrovnik,Putnik d.o.o.,Minivan transfer,Mostar -  Dubrovnik,440,880,RPO,20,6226,12,15,8,41
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Airport Split,500,1000,RPO,20,6229,12,15,8,42
Dubrovnik,Putnik d.o.o.,Minivan transfer,Airport Split -  Dubrovnik,500,1000,RPO,20,6230,12,15,8,43
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Makarska,420,840,RPO,20,6233,12,15,8,44
Dubrovnik,Putnik d.o.o.,Minivan transfer,Makarska  - Dubrovnik,420,840,RPO,20,6234,12,15,8,45
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Airport Čilipi (24H),84,168,RPO,20,6237,12,15,8,46
Dubrovnik,Putnik d.o.o.,Minivan transfer,Airport Čilipi - Dubrovnik  (24H),84,168,RPO,20,6238,12,15,8,47
Dubrovnik,Putnik d.o.o.,Car Transfer,Dubrovnik - Airport Čilipi (Included in price) President,55,110,PPOM,10,6239,1,6,4,114
Dubrovnik,Putnik d.o.o.,Car Transfer,Airport Čilipi - Dubrovnik (Included in price) President,55,110,PPOM,10,6240,1,6,4,115
Dubrovnik,Putnik d.o.o.,Car Transfer,Dubrovnik - Airport Čilipi (Included in price) Argosy,42,84,PPOM,10,6239,1,6,4,116
Dubrovnik,Putnik d.o.o.,Car Transfer,Airport Čilipi - Dubrovnik (Included in price) Argosy,42,84,PPOM,10,6240,1,6,4,117
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Airport Čilipi,76,152,RPO,20,6217,1,7,4,51
Dubrovnik,Putnik d.o.o.,Minivan transfer,Airport Čilipi - Dubrovnik ,76,152,RPO,20,6218,1,7,4,52
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Ston / Orebić,340,680,RPO,20,6221,1,7,4,53
Dubrovnik,Putnik d.o.o.,Minivan transfer,Ston / Orebić - Dubrovnik,340,680,RPO,20,6222,1,7,4,54
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Mostar,440,880,RPO,20,6225,1,7,4,55
Dubrovnik,Putnik d.o.o.,Minivan transfer,Mostar -  Dubrovnik,440,880,RPO,20,6226,1,7,4,56
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Airport Split,500,1000,RPO,20,6229,1,7,4,57
Dubrovnik,Putnik d.o.o.,Minivan transfer,Airport Split -  Dubrovnik,500,1000,RPO,20,6230,1,7,4,58
Dubrovnik,Putnik d.o.o.,Minivan transfer,Dubrovnik - Makarska,420,840,RPO,20,6233,1,7,4,60
Dubrovnik,Putnik d.o.o.,Minivan transfer,Makarska  - Dubrovnik,420,840,RPO,20,6234,1,7,4,61
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Čilipi,58,116,RPO,20,6215,1,6,4,51
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Čilipi - Dubrovnik ,58,116,RPO,20,6216,1,6,4,52
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Ston / Orebić,280,560,RPO,20,6219,1,6,4,53
Dubrovnik,Putnik d.o.o.,Car transfer,Ston / Orebić - Dubrovnik,280,560,RPO,20,6220,1,6,4,54
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Mostar,380,760,RPO,20,6223,1,6,4,55
Dubrovnik,Putnik d.o.o.,Car transfer,Mostar -  Dubrovnik,380,760,RPO,20,6224,1,6,4,56
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Split,440,880,RPO,20,6227,1,6,4,57
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Split -  Dubrovnik,440,880,RPO,20,6228,1,6,4,58
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Makarska,360,720,RPO,20,6231,1,6,4,60
Dubrovnik,Putnik d.o.o.,Car transfer,Makarska  - Dubrovnik,360,720,RPO,20,6232,1,6,4,61
Dubrovnik,Putnik d.o.o.,Car transfer,Dubrovnik - Airport Čilipi (24H),67,134,RPO,20,6235,1,6,4,112
Dubrovnik,Putnik d.o.o.,Car transfer,Airport Čilipi - Dubrovnik  (24H),67,134,RPO,20,6236,1,6,4,113
Dubrovnik,Putnik d.o.o.,Car Transfer_Included in price,Dubrovnik - Airport Čilipi,0,0,PPOM,0,0,1,22,4,51
Dubrovnik,Putnik d.o.o.,Car Transfer_Included in price,Dubrovnik - Airport Čilipi (Included in price) President,55,110,PPOM,10,6239,1,22,4,114
Dubrovnik,Putnik d.o.o.,Car Transfer_Included in price,Airport Čilipi - Dubrovnik (Included in price) President,55,110,PPOM,10,6240,1,22,4,115
Dubrovnik,Putnik d.o.o.,Car Transfer_Included in price,Dubrovnik - Airport Čilipi (Included in price) Argosy,42,84,PPOM,10,6239,1,22,4,116
Dubrovnik,Putnik d.o.o.,Car Transfer_Included in price,Airport Čilipi - Dubrovnik (Included in price) Argosy,42,84,PPOM,10,6240,1,22,4,117
Krk,Olivari,Car Transfer,Baška - Airport Krk (Rijeka/Krk),90,180,RPO,20,6205,5,24,11,62
Krk,Olivari,Car Transfer,Airport Krk (Rijeka/Krk) - Baška,90,180,RPO,20,6206,5,24,11,63
Krk,Olivari,Car Transfer,Krk - Airport Pula,320,640,RPO,20,6149,5,24,11,64
Krk,Olivari,Car Transfer,Airport Pula - Krk,320,640,RPO,20,6150,5,24,11,65
Krk,Olivari,Car Transfer,Krk - Airport Marco Polo (Venice),540,1080,RPO,20,6157,5,24,11,66
Krk,Olivari,Car Transfer,Airport Marco Polo (Venice) - Krk,540,1080,RPO,20,6158,5,24,11,67
Krk,Olivari,Car Transfer,Krk - Airport Ronchi dei Legionari (Trst),350,700,RPO,20,6153,5,24,11,88
Krk,Olivari,Car Transfer,Airport Ronchi dei Legionari (Trst) - Krk,350,700,RPO,20,6154,5,24,11,89
Krk,Olivari,Car Transfer,Krk - Airport Treviso - Sant`Angelo,540,1080,RPO,20,6161,5,24,11,90
Krk,Olivari,Car Transfer,Airport Treviso - Sant`Angelo - Krk,540,1080,RPO,20,6162,5,24,11,91
Krk,Olivari,Car Transfer,Krk - Airport Joze Pucnik (Ljubljana),420,840,RPO,20,6165,5,24,11,92
Krk,Olivari,Car Transfer,Airport Joze Pucnik (Ljubljana) - Krk,420,840,RPO,20,6166,5,24,11,93
Krk,Olivari,Car Transfer,Krk - Airport Franjo Tuđman (Zagreb),350,700,RPO,20,6169,5,24,11,94
Krk,Olivari,Car Transfer,Airport Franjo Tuđman (Zagreb) - Krk,350,700,RPO,20,6170,5,24,11,95
Krk,Olivari,Car Transfer,Krk - Airport Rijeka (Rijeka/Krk),70,140,RPO,20,6173,5,24,11,96
Krk,Olivari,Car Transfer,Airport Rijeka (Rijeka/Krk) - Krk,70,140,RPO,20,6174,5,24,11,97
Krk,Olivari,Car Transfer,Baška - Airport Pula,340,680,RPO,20,6181,5,24,11,98
Krk,Olivari,Car Transfer,Airport Pula - Baška ,340,680,RPO,20,6182,5,24,11,99
Krk,Olivari,Car Transfer,Baška - Airport Marco Polo (Venice),560,1120,RPO,20,6189,5,24,11,100
Krk,Olivari,Car Transfer,Airport Marco Polo (Venice) - Baška ,560,1120,RPO,20,6190,5,24,11,101
Krk,Olivari,Car Transfer,Baška - Airport Ronchi dei Legionari (Trst),370,740,RPO,20,6185,5,24,11,102
Krk,Olivari,Car Transfer,Airport Ronchi dei Legionari (Trst) - Baška ,370,740,RPO,20,6186,5,24,11,103
Krk,Olivari,Car Transfer,Baška - Airport Treviso - Sant`Angelo,560,1120,RPO,20,6193,5,24,11,104
Krk,Olivari,Car Transfer,Airport Treviso - Sant`Angelo - Baška ,560,1120,RPO,20,6194,5,24,11,105
Krk,Olivari,Car Transfer,Baška - Airport Joze Pucnik (Ljubljana),450,900,RPO,20,6197,5,24,11,106
Krk,Olivari,Car Transfer,Airport Joze Pucnik (Ljubljana) - Baška ,450,900,RPO,20,6198,5,24,11,107
Krk,Olivari,Car Transfer,Baška - Airport Franjo Tuđman (Zagreb),390,780,RPO,20,6201,5,24,11,108
Krk,Olivari,Car Transfer,Airport Franjo Tuđman (Zagreb) - Baška ,390,780,RPO,20,6202,5,24,11,109
Krk,Olivari,Minivan Transfer,Baška - Airport Krk (Rijeka/Krk),110,220,RPO,20,6207,5,25,11,62
Krk,Olivari,Minivan Transfer,Airport Krk (Rijeka/Krk) - Baška,110,220,RPO,20,6208,5,25,11,63
Krk,Olivari,Minivan Transfer,Krk - Airport Pula,400,800,RPO,20,6151,5,25,11,64
Krk,Olivari,Minivan Transfer,Airport Pula - Krk,400,800,RPO,20,6152,5,25,11,65
Krk,Olivari,Minivan Transfer,Krk - Airport Marco Polo (Venice),620,1240,RPO,20,6159,5,25,11,66
Krk,Olivari,Minivan Transfer,Airport Marco Polo (Venice) - Krk,620,1240,RPO,20,6160,5,25,11,67
Krk,Olivari,Minivan Transfer,Krk - Airport Ronchi dei Legionari (Trst),390,780,RPO,20,6155,5,25,11,88
Krk,Olivari,Minivan Transfer,Airport Ronchi dei Legionari (Trst) - Krk,390,780,RPO,20,6156,5,25,11,89
Krk,Olivari,Minivan Transfer,Krk - Airport Treviso - Sant`Angelo,620,1240,RPO,20,6163,5,25,11,90
Krk,Olivari,Minivan Transfer,Airport Treviso - Sant`Angelo - Krk,620,1240,RPO,20,6164,5,25,11,91
Krk,Olivari,Minivan Transfer,Krk - Airport Joze Pucnik (Ljubljana),490,1080,RPO,20,6167,5,25,11,92
Krk,Olivari,Minivan Transfer,Airport Joze Pucnik (Ljubljana) - Krk,490,1080,RPO,20,6168,5,25,11,93
Krk,Olivari,Minivan Transfer,Krk - Airport Franjo Tuđman (Zagreb),420,840,RPO,20,6171,5,25,11,94
Krk,Olivari,Minivan Transfer,Airport Franjo Tuđman (Zagreb) - Krk,420,840,RPO,20,6172,5,25,11,95
Krk,Olivari,Minivan Transfer,Krk - Airport Rijeka (Rijeka/Krk),90,180,RPO,20,6175,5,25,11,96
Krk,Olivari,Minivan Transfer,Airport Rijeka (Rijeka/Krk) - Krk,90,180,RPO,20,6176,5,25,11,97
Krk,Olivari,Minivan Transfer,Baška - Airport Pula,420,840,RPO,20,6183,5,25,11,98
Krk,Olivari,Minivan Transfer,Airport Pula - Baška ,420,840,RPO,20,6184,5,25,11,99
Krk,Olivari,Minivan Transfer,Baška - Airport Marco Polo (Venice),640,1280,RPO,20,6191,5,25,11,100
Krk,Olivari,Minivan Transfer,Airport Marco Polo (Venice) - Baška ,640,1280,RPO,20,6192,5,25,11,101
Krk,Olivari,Minivan Transfer,Baška - Airport Ronchi dei Legionari (Trst),420,840,RPO,20,6187,5,25,11,102
Krk,Olivari,Minivan Transfer,Airport Ronchi dei Legionari (Trst) - Baška ,420,840,RPO,20,6188,5,25,11,103
Krk,Olivari,Minivan Transfer,Baška - Airport Treviso - Sant`Angelo,650,1300,RPO,20,6195,5,25,11,104
Krk,Olivari,Minivan Transfer,Airport Treviso - Sant`Angelo - Baška ,650,1300,RPO,20,6196,5,25,11,105
Krk,Olivari,Minivan Transfer,Baška - Airport Joze Pucnik (Ljubljana),520,1040,RPO,20,6199,5,25,11,106
Krk,Olivari,Minivan Transfer,Airport Joze Pucnik (Ljubljana) - Baška ,520,1040,RPO,20,6200,5,25,11,107
Krk,Olivari,Minivan Transfer,Baška - Airport Franjo Tuđman (Zagreb),450,900,RPO,20,6203,5,25,11,108
Krk,Olivari,Minivan Transfer,Airport Franjo Tuđman (Zagreb) - Baška ,450,900,RPO,20,6204,5,25,11,109
Makarska,Simply Adria,Car transfer,Makarska - Split,120,240,RPO,20,6263,13,26,12,68
Makarska,Simply Adria,Car transfer,Split - Makarska,120,240,RPO,20,6264,13,26,12,69
Makarska,Simply Adria,Car transfer,Makarska - Dubrovnik,260,520,RPO,20,6269,13,26,12,70
Makarska,Simply Adria,Car transfer,Dubrovnik - Makarska ,260,520,RPO,20,6270,13,26,12,71
Makarska,Simply Adria,Car transfer,Makarska - Zadar,290,580,RPO,20,6275,13,26,12,72
Makarska,Simply Adria,Car transfer,Zadar - Makarska,290,580,RPO,20,6276,13,26,12,73
Makarska,Simply Adria,Minivan transfer,Makarska - Split,130,260,RPO,20,6265,13,27,12,68
Makarska,Simply Adria,Minivan transfer,Split - Makarska,130,260,RPO,20,6266,13,27,12,69
Makarska,Simply Adria,Minivan transfer,Makarska - Dubrovnik,300,600,RPO,20,6271,13,27,12,70
Makarska,Simply Adria,Minivan transfer,Dubrovnik - Makarska ,300,600,RPO,20,6272,13,27,12,71
Makarska,Simply Adria,Minivan transfer,Makarska - Zadar,300,600,RPO,20,6277,13,27,12,72
Makarska,Simply Adria,Minivan transfer,Zadar - Makarska,300,600,RPO,20,6278,13,27,12,73
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Pula,125,250,RPO,20,6001,3,2,2,2
Poreč,Babić transport d.o.o.,Car transfer,Airport Pula - Poreč ,125,250,RPO,20,6002,3,2,2,3
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Ronchi dei Legionari (Trst),300,600,RPO,20,6005,3,2,2,4
Poreč,Babić transport d.o.o.,Car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,300,600,RPO,20,6006,3,2,2,5
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Marco Polo (Venice),440,880,RPO,20,6009,3,2,2,6
Poreč,Babić transport d.o.o.,Car transfer,Airport Marco Polo (Venice) - Poreč,440,880,RPO,20,6010,3,2,2,7
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Treviso-Sant`Angelo,440,880,RPO,20,6013,3,2,2,8
Poreč,Babić transport d.o.o.,Car transfer,Airport Treviso-Sant`Angelo - Poreč,440,880,RPO,20,6014,3,2,2,9
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Joze Pucnik (Ljubljana),440,880,RPO,20,6017,3,2,2,10
Poreč,Babić transport d.o.o.,Car transfer,Airport Joze Pucnik (Ljubljana) - Poreč,440,880,RPO,20,6018,3,2,2,11
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Franjo Ruđman (Zagreb),440,880,RPO,20,6021,3,2,2,12
Poreč,Babić transport d.o.o.,Car transfer,Airport Franjo Ruđman (Zagreb) - Poreč,440,880,RPO,20,6022,3,2,2,13
Poreč,Babić transport d.o.o.,Car transfer,Poreč - Airport Rijeka (Rijeka/Krk),300,600,RPO,20,6025,3,2,2,14
Poreč,Babić transport d.o.o.,Car transfer,Airport Rijeka (Rijeka/Krk) - Poreč,300,600,RPO,20,6026,3,2,2,15
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Pula,150,300,RPO,20,6003,3,4,2,2
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Pula - Poreč ,150,300,RPO,20,6004,3,4,2,3
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Ronchi dei Legionari (Trst),300,600,RPO,20,6007,3,4,2,4
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Ronchi dei Legionari (Trst) - Poreč,300,600,RPO,20,6008,3,4,2,5
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Marco Polo (Venice),500,1000,RPO,20,6011,3,4,2,6
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Marco Polo (Venice) - Poreč,500,1000,RPO,20,6012,3,4,2,7
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Treviso-Sant`Angelo,500,1000,RPO,20,6015,3,4,2,8
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Treviso-Sant`Angelo - Poreč,500,1000,RPO,20,6016,3,4,2,9
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Joze Pucnik (Ljubljana),500,1000,RPO,20,6019,3,4,2,10
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Joze Pucnik (Ljubljana) - Poreč,500,1000,RPO,20,6020,3,4,2,11
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Franjo Ruđman (Zagreb),500,1000,RPO,20,6023,3,4,2,12
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Franjo Ruđman (Zagreb) - Poreč,500,1000,RPO,20,6024,3,4,2,13
Poreč,Babić transport d.o.o.,Transfer kombijem,Poreč - Airport Rijeka (Rijeka/Krk),350,700,RPO,20,6027,3,4,2,14
Poreč,Babić transport d.o.o.,Transfer kombijem,Airport Rijeka (Rijeka/Krk) - Poreč,350,700,RPO,20,6028,3,4,2,15
Poreč,Greenway,Car transfer,Poreč - Airport Pula,100,200,RPO,20,6029,3,2,3,2
Poreč,Greenway,Car transfer,Airport Pula - Poreč ,100,200,RPO,20,6030,3,2,3,3
Poreč,Greenway,Car transfer,Poreč - Airport Ronchi dei Legionari (Trst),200,400,RPO,20,6035,3,2,3,4
Poreč,Greenway,Car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,200,400,RPO,20,6036,3,2,3,5
Poreč,Greenway,Car transfer,Poreč - Airport Marco Polo (Venice),380,760,RPO,20,6041,3,2,3,6
Poreč,Greenway,Car transfer,Airport Marco Polo (Venice) - Poreč,380,760,RPO,20,6042,3,2,3,7
Poreč,Greenway,Car transfer,Poreč - Airport Treviso-Sant`Angelo,380,760,RPO,20,6047,3,2,3,8
Poreč,Greenway,Car transfer,Airport Treviso-Sant`Angelo - Poreč,380,760,RPO,20,6048,3,2,3,9
Poreč,Greenway,Car transfer,Poreč - Airport Joze Pucnik (Ljubljana),280,560,RPO,20,6053,3,2,3,10
Poreč,Greenway,Car transfer,Airport Joze Pucnik (Ljubljana) - Poreč,280,560,RPO,20,6054,3,2,3,11
Poreč,Greenway,Car transfer,Poreč - Airport Franjo Ruđman (Zagreb),380,760,RPO,20,6059,3,2,3,12
Poreč,Greenway,Car transfer,Airport Franjo Ruđman (Zagreb) - Poreč,380,760,RPO,20,6060,3,2,3,13
Poreč,Greenway,Car transfer,Poreč - Airport Rijeka (Rijeka/Krk),200,400,RPO,20,6065,3,2,3,14
Poreč,Greenway,Car transfer,Airport Rijeka (Rijeka/Krk) - Poreč,200,400,RPO,20,6066,3,2,3,15
Poreč,Greenway,Luxury car transfer,Poreč - Airport Pula,140,280,RPO,20,6031,3,3,3,2
Poreč,Greenway,Luxury car transfer,Airport Pula - Poreč ,140,280,RPO,20,6032,3,3,3,3
Poreč,Greenway,Luxury car transfer,Poreč - Airport Ronchi dei Legionari (Trst),250,500,RPO,20,6037,3,3,3,4
Poreč,Greenway,Luxury car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,250,500,RPO,20,6038,3,3,3,5
Poreč,Greenway,Luxury car transfer,Poreč - Airport Marco Polo (Venice),450,900,RPO,20,6043,3,3,3,6
Poreč,Greenway,Luxury car transfer,Airport Marco Polo (Venice) - Poreč,450,900,RPO,20,6044,3,3,3,7
Poreč,Greenway,Luxury car transfer,Poreč - Airport Treviso-Sant`Angelo,450,900,RPO,20,6049,3,3,3,8
Poreč,Greenway,Luxury car transfer,Airport Treviso-Sant`Angelo - Poreč,450,900,RPO,20,6050,3,3,3,9
Poreč,Greenway,Luxury car transfer,Poreč - Airport Joze Pucnik (Ljubljana),350,700,RPO,20,6055,3,3,3,10
Poreč,Greenway,Luxury car transfer,Airport Joze Pucnik (Ljubljana) - Poreč,350,700,RPO,20,6056,3,3,3,11
Poreč,Greenway,Luxury car transfer,Poreč - Airport Franjo Ruđman (Zagreb),450,900,RPO,20,6061,3,3,3,12
Poreč,Greenway,Luxury car transfer,Airport Franjo Ruđman (Zagreb) - Poreč,450,900,RPO,20,6062,3,3,3,13
Poreč,Greenway,Luxury car transfer,Poreč - Airport Rijeka (Rijeka/Krk),250,500,RPO,20,6067,3,3,3,14
Poreč,Greenway,Luxury car transfer,Airport Rijeka (Rijeka/Krk) - Poreč,250,500,RPO,20,6068,3,3,3,15
Poreč,Greenway,Transfer kombijem,Poreč - Airport Pula,120,240,RPO,20,6033,3,4,3,2
Poreč,Greenway,Transfer kombijem,Airport Pula - Poreč ,120,240,RPO,20,6034,3,4,3,3
Poreč,Greenway,Transfer kombijem,Poreč - Airport Ronchi dei Legionari (Trst),220,440,RPO,20,6039,3,4,3,4
Poreč,Greenway,Transfer kombijem,Airport Ronchi dei Legionari (Trst) - Poreč,220,440,RPO,20,6040,3,4,3,5
Poreč,Greenway,Transfer kombijem,Poreč - Airport Marco Polo (Venice),420,840,RPO,20,6045,3,4,3,6
Poreč,Greenway,Transfer kombijem,Airport Marco Polo (Venice) - Poreč,420,840,RPO,20,6046,3,4,3,7
Poreč,Greenway,Transfer kombijem,Poreč - Airport Treviso-Sant`Angelo,420,840,RPO,20,6051,3,4,3,8
Poreč,Greenway,Transfer kombijem,Airport Treviso-Sant`Angelo - Poreč,420,840,RPO,20,6052,3,4,3,9
Poreč,Greenway,Transfer kombijem,Poreč - Airport Joze Pucnik (Ljubljana),320,640,RPO,20,6057,3,4,3,10
Poreč,Greenway,Transfer kombijem,Airport Joze Pucnik (Ljubljana) - Poreč,320,640,RPO,20,6058,3,4,3,11
Poreč,Greenway,Transfer kombijem,Poreč - Airport Franjo Ruđman (Zagreb),420,840,RPO,20,6063,3,4,3,12
Poreč,Greenway,Transfer kombijem,Airport Franjo Ruđman (Zagreb) - Poreč,420,840,RPO,20,6064,3,4,3,13
Poreč,Greenway,Transfer kombijem,Poreč - Airport Rijeka (Rijeka/Krk),220,440,RPO,20,6069,3,4,3,14
Poreč,Greenway,Transfer kombijem,Airport Rijeka (Rijeka/Krk) - Poreč,220,440,RPO,20,6070,3,4,3,15
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Pula,170,340,RPO,20,6241,3,5,3,2
Poreč,Greenway,Transfer luksuznim kombijem,Airport Pula - Poreč ,170,340,RPO,20,6242,3,5,3,3
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Ronchi dei Legionari (Trst),280,560,RPO,20,6243,3,5,3,4
Poreč,Greenway,Transfer luksuznim kombijem,Airport Ronchi dei Legionari (Trst) - Poreč,280,560,RPO,20,6244,3,5,3,5
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Marco Polo (Venice),480,960,RPO,20,6245,3,5,3,6
Poreč,Greenway,Transfer luksuznim kombijem,Airport Marco Polo (Venice) - Poreč,480,960,RPO,20,6246,3,5,3,7
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Treviso-Sant`Angelo,480,960,RPO,20,6247,3,5,3,8
Poreč,Greenway,Transfer luksuznim kombijem,Airport Treviso-Sant`Angelo - Poreč,480,960,RPO,20,6248,3,5,3,9
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Joze Pucnik (Ljubljana),380,760,RPO,20,6249,3,5,3,10
Poreč,Greenway,Transfer luksuznim kombijem,Airport Joze Pucnik (Ljubljana) - Poreč,380,760,RPO,20,6250,3,5,3,11
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Franjo Ruđman (Zagreb),480,960,RPO,20,6251,3,5,3,12
Poreč,Greenway,Transfer luksuznim kombijem,Airport Franjo Ruđman (Zagreb) - Poreč,480,960,RPO,20,6252,3,5,3,13
Poreč,Greenway,Transfer luksuznim kombijem,Poreč - Airport Rijeka (Rijeka/Krk),280,560,RPO,20,6253,3,5,3,14
Poreč,Greenway,Transfer luksuznim kombijem,Airport Rijeka (Rijeka/Krk) - Poreč,280,560,RPO,20,6254,3,5,3,15
Poreč,Greenway ,Car transfer,Poreč - Airport Pula,100,200,RPO,20,6029,11,10,7,22
Poreč,Greenway ,Car transfer,Airport Pula - Poreč ,100,200,RPO,20,6030,11,10,7,23
Poreč,Greenway ,Car transfer,Poreč - Airport Ronchi dei Legionari (Trst),200,400,RPO,20,6035,11,10,7,24
Poreč,Greenway ,Car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,200,400,RPO,20,6036,11,10,7,25
Poreč,Greenway ,Car transfer,Airport Marco Polo (Venecija) - Poreč,380,760,RPO,20,6042,11,10,7,26
Poreč,Greenway ,Car transfer,Poreč - Airport Marco Polo (Venecija) ,380,760,RPO,20,6041,11,10,7,27
Poreč,Greenway ,Car transfer,Poreč - Airport Joze Pucnik (Ljubljana),280,560,RPO,20,6053,11,10,7,28
Poreč,Greenway ,Car transfer,Airport Joze Pucnik (Ljubljana) - Poreč - ,280,560,RPO,20,6054,11,10,7,29
Poreč,Greenway ,Car transfer,Airport Franjo Tuđman (Zagreb) - Poreč,380,760,RPO,20,6060,11,10,7,30
Poreč,Greenway ,Car transfer,Poreč  - Airport Franjo Tuđman (Zagreb),380,760,RPO,20,6059,11,10,7,31
Poreč,Greenway ,Car transfer,Poreč  - Airport Rijeka (Rijeka/Krk),200,400,RPO,20,6065,11,10,7,32
Poreč,Greenway ,Car transfer,Airport Rijeka (Rijeka/Krk) - Poreč ,200,400,RPO,20,6066,11,10,7,33
Poreč,Greenway ,Car transfer,Poreč - Airport Treviso-Sant`Angelo,380,760,RPO,20,6047,11,10,7,110
Poreč,Greenway ,Car transfer,Airport Treviso-Sant`Angelo - Poreč,380,760,RPO,20,6048,11,10,7,111
Poreč,Greenway ,Luxury car transfer,Poreč - Airport Pula,140,280,RPO,20,6031,11,11,7,22
Poreč,Greenway ,Luxury car transfer,Airport Pula - Poreč ,140,280,RPO,20,6032,11,11,7,23
Poreč,Greenway ,Luxury car transfer,Poreč - Airport Ronchi dei Legionari (Trst),250,500,RPO,20,6037,11,11,7,24
Poreč,Greenway ,Luxury car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,250,500,RPO,20,6038,11,11,7,25
Poreč,Greenway ,Luxury car transfer,Airport Marco Polo (Venecija) - Poreč,450,900,RPO,20,6044,11,11,7,26
Poreč,Greenway ,Luxury car transfer,Poreč - Airport Marco Polo (Venecija) ,450,900,RPO,20,6043,11,11,7,27
Poreč,Greenway ,Luxury car transfer,Poreč - Airport Joze Pucnik (Ljubljana),350,700,RPO,20,6055,11,11,7,28
Poreč,Greenway ,Luxury car transfer,Airport Joze Pucnik (Ljubljana) - Poreč - ,350,700,RPO,20,6056,11,11,7,29
Poreč,Greenway ,Luxury car transfer,Airport Franjo Ruđman (Zagreb) - Poreč ,450,900,RPO,20,6062,11,11,7,30
Poreč,Greenway ,Luxury car transfer,Poreč  - Airport Franjo Ruđman (Zagreb),450,900,RPO,20,6061,11,11,7,31
Poreč,Greenway ,Luxury car transfer,Poreč  - Airport Rijeka (Rijeka/Krk),250,500,RPO,20,6067,11,11,7,32
Poreč,Greenway ,Luxury car transfer,Airport Rijeka (Rijeka/Krk) - Poreč ,250,500,RPO,20,6068,11,11,7,33
Poreč,Greenway ,Luxury car transfer,Poreč - Airport Treviso-Sant`Angelo,450,900,RPO,20,6049,11,11,7,110
Poreč,Greenway ,Luxury car transfer,Airport Treviso-Sant`Angelo - Poreč,450,900,RPO,20,6050,11,11,7,111
Poreč,Greenway ,Minivan transfer,Poreč - Airport Pula,120,240,RPO,20,6033,11,12,7,22
Poreč,Greenway ,Minivan transfer,Airport Pula - Poreč ,120,240,RPO,20,6034,11,12,7,23
Poreč,Greenway ,Minivan transfer,Poreč - Airport Ronchi dei Legionari (Trst),220,440,RPO,20,6039,11,12,7,24
Poreč,Greenway ,Minivan transfer,Airport Ronchi dei Legionari (Trst) - Poreč,220,440,RPO,20,6040,11,12,7,25
Poreč,Greenway ,Minivan transfer,Airport Marco Polo (Venecija) - Poreč,420,840,RPO,20,6046,11,12,7,26
Poreč,Greenway ,Minivan transfer,Poreč - Airport Marco Polo (Venecija) ,420,840,RPO,20,6045,11,12,7,27
Poreč,Greenway ,Minivan transfer,Poreč - Airport Joze Pucnik (Ljubljana),320,640,RPO,20,6057,11,12,7,28
Poreč,Greenway ,Minivan transfer,Airport Joze Pucnik (Ljubljana) - Poreč - ,320,640,RPO,20,6058,11,12,7,29
Poreč,Greenway ,Minivan transfer,Airport Franjo Ruđman (Zagreb) - Poreč ,420,840,RPO,20,6064,11,12,7,30
Poreč,Greenway ,Minivan transfer,Poreč  - Airport Franjo Ruđman (Zagreb),420,840,RPO,20,6063,11,12,7,31
Poreč,Greenway ,Minivan transfer,Poreč  - Airport Rijeka (Rijeka/Krk),220,440,RPO,20,6069,11,12,7,32
Poreč,Greenway ,Minivan transfer,Airport Rijeka (Rijeka/Krk) - Poreč ,220,440,RPO,20,6070,11,12,7,33
Poreč,Greenway ,Minivan transfer,Poreč - Airport Treviso-Sant`Angelo,420,840,RPO,20,6051,11,12,7,110
Poreč,Greenway ,Minivan transfer,Airport Treviso-Sant`Angelo - Poreč,420,840,RPO,20,6052,11,12,7,111
Poreč,Greenway ,Luxury minivan transfer,Poreč - Airport Pula,170,340,RPO,20,6241,11,13,7,22
Poreč,Greenway ,Luxury minivan transfer,Airport Pula - Poreč ,170,340,RPO,20,6242,11,13,7,23
Poreč,Greenway ,Luxury minivan transfer,Poreč - Airport Ronchi dei Legionari (Trst),280,560,RPO,20,6243,11,13,7,24
Poreč,Greenway ,Luxury minivan transfer,Airport Ronchi dei Legionari (Trst) - Poreč,280,560,RPO,20,6244,11,13,7,25
Poreč,Greenway ,Luxury minivan transfer,Airport Marco Polo (Venecija) - Poreč,480,960,RPO,20,6246,11,13,7,26
Poreč,Greenway ,Luxury minivan transfer,Poreč - Airport Marco Polo (Venecija) ,480,960,RPO,20,6245,11,13,7,27
Poreč,Greenway ,Luxury minivan transfer,Poreč - Airport Joze Pucnik (Ljubljana),380,760,RPO,20,6249,11,13,7,28
Poreč,Greenway ,Luxury minivan transfer,Airport Joze Pucnik (Ljubljana) - Poreč - ,380,760,RPO,20,6250,11,13,7,29
Poreč,Greenway ,Luxury minivan transfer,Airport Franjo Ruđman (Zagreb) - Poreč ,480,960,RPO,20,6252,11,13,7,30
Poreč,Greenway ,Luxury minivan transfer,Poreč  - Airport Franjo Ruđman (Zagreb),480,960,RPO,20,6251,11,13,7,31
Poreč,Greenway ,Luxury minivan transfer,Poreč  - Airport Rijeka (Rijeka/Krk),280,560,RPO,20,6253,11,13,7,32
Poreč,Greenway ,Luxury minivan transfer,Airport Rijeka (Rijeka/Krk) - Poreč ,280,560,RPO,20,6254,11,13,7,33
Poreč,Greenway ,Luxury minivan transfer,Poreč - Airport Treviso-Sant`Angelo,480,960,RPO,20,6247,11,13,7,110
Poreč,Greenway ,Luxury minivan transfer,Airport Treviso-Sant`Angelo - Poreč,480,960,RPO,20,6248,11,13,7,111
Poreč,Istra taxi,Car transfer,Poreč - Airport Pula,110,220,RPO,20,6073,3,2,17,2
Poreč,Istra taxi,Car transfer,Airport Pula - Poreč ,110,220,RPO,20,6074,3,2,17,3
Poreč,Istra taxi,Car transfer,Poreč - Airport Ronchi dei Legionari (Trst),200,400,RPO,20,6077,3,2,17,4
Poreč,Istra taxi,Car transfer,Airport Ronchi dei Legionari (Trst) - Poreč,200,400,RPO,20,6078,3,2,17,5
Poreč,Istra taxi,Car transfer,Poreč - Airport Marco Polo (Venice),380,760,RPO,20,6081,3,2,17,6
Poreč,Istra taxi,Car transfer,Airport Marco Polo (Venice) - Poreč,380,760,RPO,20,6082,3,2,17,7
Poreč,Istra taxi,Car transfer,Poreč - Airport Treviso-Sant`Angelo,380,760,RPO,20,6085,3,2,17,8
Poreč,Istra taxi,Car transfer,Airport Treviso-Sant`Angelo - Poreč,380,760,RPO,20,6086,3,2,17,9
Poreč,Istra taxi,Car transfer,Poreč - Airport Joze Pucnik (Ljubljana),300,600,RPO,20,6089,3,2,17,10
Poreč,Istra taxi,Car transfer,Airport Joze Pucnik (Ljubljana) - Poreč,300,600,RPO,20,6090,3,2,17,11
Poreč,Istra taxi,Car transfer,Poreč - Airport Franjo Ruđman (Zagreb),380,760,RPO,20,6093,3,2,17,12
Poreč,Istra taxi,Car transfer,Airport Franjo Ruđman (Zagreb) - Poreč,380,760,RPO,20,6094,3,2,17,13
Poreč,Istra taxi,Car transfer,Poreč - Airport Rijeka (Rijeka/Krk),200,400,RPO,20,6097,3,2,17,14
Poreč,Istra taxi,Car transfer,Airport Rijeka (Rijeka/Krk) - Poreč,200,400,RPO,20,6098,3,2,17,15
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Pula,120,240,RPO,20,6075,3,4,17,2
Poreč,Istra taxi,Transfer kombijem,Airport Pula - Poreč ,120,240,RPO,20,6076,3,4,17,3
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Ronchi dei Legionari (Trst),220,440,RPO,20,6079,3,4,17,4
Poreč,Istra taxi,Transfer kombijem,Airport Ronchi dei Legionari (Trst) - Poreč,220,440,RPO,20,6080,3,4,17,5
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Marco Polo (Venice),420,840,RPO,20,6083,3,4,17,6
Poreč,Istra taxi,Transfer kombijem,Airport Marco Polo (Venice) - Poreč,420,840,RPO,20,6084,3,4,17,7
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Treviso-Sant`Angelo,420,840,RPO,20,6087,3,4,17,8
Poreč,Istra taxi,Transfer kombijem,Airport Treviso-Sant`Angelo - Poreč,420,840,RPO,20,6088,3,4,17,9
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Joze Pucnik (Ljubljana),320,640,RPO,20,6091,3,4,17,10
Poreč,Istra taxi,Transfer kombijem,Airport Joze Pucnik (Ljubljana) - Poreč,320,640,RPO,20,6092,3,4,17,11
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Franjo Ruđman (Zagreb),420,840,RPO,20,6095,3,4,17,12
Poreč,Istra taxi,Transfer kombijem,Airport Franjo Ruđman (Zagreb) - Poreč,420,840,RPO,20,6096,3,4,17,13
Poreč,Istra taxi,Transfer kombijem,Poreč - Airport Rijeka (Rijeka/Krk),220,440,RPO,20,6099,3,4,17,14
Poreč,Istra taxi,Transfer kombijem,Airport Rijeka (Rijeka/Krk) - Poreč,220,440,RPO,20,6100,3,4,17,15
Rab,Rab Tours,Car Transfer,Rab - Airport Rijeka (Rijeka/Krk),180,360,RPO,20,6281,16,33,18,118
Rab,Rab Tours,Car Transfer,Airport Rijeka (Rijeka/Krk) - Rab,180,360,RPO,20,6282,16,33,18,119
Rab,Rab Tours,Car Transfer,Rab - Airport Zadar,200,400,RPO,20,6285,16,33,18,120
Rab,Rab Tours,Car Transfer,Airport Zadar - Rab,200,400,RPO,20,6286,16,33,18,121
Rab,Rab Tours,Car Transfer,Rab - Airport Franjo Tuđman (Zagreb),250,500,RPO,20,6289,16,33,18,122
Rab,Rab Tours,Car Transfer,Airport Franjo Tuđman (Zagreb) - Rab,250,500,RPO,20,6290,16,33,18,123
Rab,Rab Tours,Car Transfer,Rab - Airport Split,270,540,RPO,20,6293,16,33,18,124
Rab,Rab Tours,Car Transfer,Airport Split - Rab,270,540,RPO,20,6294,16,33,18,125
Rab,Rab Tours,Car Transfer,Rab - Airport Pula,260,520,RPO,20,6297,16,33,18,126
Rab,Rab Tours,Car Transfer,Airport Pula - Rab,260,520,RPO,20,6298,16,33,18,127
Rab,Rab Tours,Minivan Transfer,Rab - Airport Rijeka (Rijeka/Krk),240,480,RPO,20,6283,16,34,18,118
Rab,Rab Tours,Minivan Transfer,Airport Rijeka (Rijeka/Krk) - Rab,240,480,RPO,20,6284,16,34,18,119
Rab,Rab Tours,Minivan Transfer,Rab - Airport Zadar,260,520,RPO,20,6287,16,34,18,120
Rab,Rab Tours,Minivan Transfer,Airport Zadar - Rab,260,520,RPO,20,6288,16,34,18,121
Rab,Rab Tours,Minivan Transfer,Rab - Airport Franjo Tuđman (Zagreb),300,600,RPO,20,6291,16,34,18,122
Rab,Rab Tours,Minivan Transfer,Airport Franjo Tuđman (Zagreb) - Rab,300,600,RPO,20,6292,16,34,18,123
Rab,Rab Tours,Minivan Transfer,Rab - Airport Split,320,640,RPO,20,6295,16,34,18,124
Rab,Rab Tours,Minivan Transfer,Airport Split - Rab,320,640,RPO,20,6296,16,34,18,125
Rab,Rab Tours,Minivan Transfer,Rab - Airport Pula,310,620,RPO,20,6299,16,34,18,126
Rab,Rab Tours,Minivan Transfer,Airport Pula - Rab,310,620,RPO,20,6300,16,34,18,127
Rabac,Greenway,Luxury Minivan Transfer,Airport Pula - Rabac,170,340,RPO,20,6108,4,31,3,20
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Pula,170,340,RPO,20,6107,4,31,3,21
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Marco Polo,550,1100,RPO,20,6123,4,31,3,74
Rabac,Greenway,Luxury Minivan Transfer,Airport Marco Polo - Rabac ,550,1100,RPO,20,6124,4,31,3,75
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Ronchi dei Legionari (Trst),350,700,RPO,20,6115,4,31,3,76
Rabac,Greenway,Luxury Minivan Transfer, Airport Ronchi dei Legionari (Trst) - Rabac,350,700,RPO,20,6116,4,31,3,77
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport San`t Angelo (Treviso),550,1100,RPO,20,6261,4,31,3,80
Rabac,Greenway,Luxury Minivan Transfer,Airport San`t Angelo (Treviso) - Rabac,550,1100,RPO,20,6262,4,31,3,81
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Joze Pucnik (Ljubljana),420,840,RPO,20,6131,4,31,3,82
Rabac,Greenway,Luxury Minivan Transfer,Airport Joze Pucnik (Ljubljana) - Rabac,420,840,RPO,20,6132,4,31,3,83
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Franjo Tuđman (Zagreb),520,1040,RPO,20,6139,4,31,3,84
Rabac,Greenway,Luxury Minivan Transfer,Airport Franjo Tuđman (Zagreb) - Rabac,520,1040,RPO,20,6140,4,31,3,85
Rabac,Greenway,Luxury Minivan Transfer,Rabac - Airport Rijeka (RIjeka/Krk),270,540,RPO,20,6147,4,31,3,86
Rabac,Greenway,Luxury Minivan Transfer,Airport Rijeka (Rijeka/Krk) - Rabac,270,540,RPO,20,6148,4,31,3,87
Rabac,Greenway,Luxury Car Transfer,Airport Pula - Rabac,140,280,RPO,20,6104,4,32,3,20
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Pula,140,280,RPO,20,6103,4,32,3,21
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Marco Polo,500,1000,RPO,20,6119,4,32,3,74
Rabac,Greenway,Luxury Car Transfer,Airport Marco Polo - Rabac ,500,1000,RPO,20,6120,4,32,3,75
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Ronchi dei Legionari (Trst),310,620,RPO,20,6111,4,32,3,76
Rabac,Greenway,Luxury Car Transfer, Airport Ronchi dei Legionari (Trst) - Rabac,310,620,RPO,20,6112,4,32,3,77
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport San`t Angelo (Treviso),500,1000,RPO,20,6257,4,32,3,80
Rabac,Greenway,Luxury Car Transfer,Airport San`t Angelo (Treviso) - Rabac,500,1000,RPO,20,6258,4,32,3,81
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Joze Pucnik (Ljubljana),380,760,RPO,20,6127,4,32,3,82
Rabac,Greenway,Luxury Car Transfer,Airport Joze Pucnik (Ljubljana) - Rabac,380,760,RPO,20,6128,4,32,3,83
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Franjo Tuđman (Zagreb),470,940,RPO,20,6135,4,32,3,84
Rabac,Greenway,Luxury Car Transfer,Airport Franjo Tuđman (Zagreb) - Rabac,470,940,RPO,20,6136,4,32,3,85
Rabac,Greenway,Luxury Car Transfer,Rabac - Airport Rijeka (RIjeka/Krk),230,460,RPO,20,6143,4,32,3,86
Rabac,Greenway,Luxury Car Transfer,Airport Rijeka (RIjeka/Krk) - Rabac,230,460,RPO,20,6144,4,32,3,87
Rabac,Greenway,Car Transfer,Airport Pula - Rabac,100,200,RPO,20,6102,4,8,3,20
Rabac,Greenway,Car Transfer,Rabac - Airport Pula,100,200,RPO,20,6101,4,8,3,21
Rabac,Greenway,Car Transfer,Rabac - Airport Marco Polo,440,880,RPO,20,6117,4,8,3,74
Rabac,Greenway,Car Transfer,Airport Marco Polo - Rabac ,440,880,RPO,20,6118,4,8,3,75
Rabac,Greenway,Car Transfer,Rabac - Airport Ronchi dei Legionari (Trst),250,500,RPO,20,6109,4,8,3,76
Rabac,Greenway,Car Transfer, Airport Ronchi dei Legionari (Trst) - Rabac,250,500,RPO,20,6110,4,8,3,77
Rabac,Greenway,Car Transfer,Rabac - Airport San`t Angelo (Treviso),440,880,RPO,20,6255,4,8,3,80
Rabac,Greenway,Car Transfer,Airport San`t Angelo (Treviso) - Rabac,440,880,RPO,20,6256,4,8,3,81
Rabac,Greenway,Car Transfer,Rabac - Airport Joze Pucnik (Ljubljana),320,640,RPO,20,6125,4,8,3,82
Rabac,Greenway,Car Transfer,Airport Joze Pucnik (Ljubljana) - Rabac,320,640,RPO,20,6126,4,8,3,83
Rabac,Greenway,Car Transfer,Rabac - Airport Franjo Tuđman (Zagreb),400,800,RPO,20,6133,4,8,3,84
Rabac,Greenway,Car Transfer,Airport Franjo Tuđman (Zagreb) - Rabac,400,800,RPO,20,6134,4,8,3,85
Rabac,Greenway,Car Transfer,Rabac - Airport Rijeka (RIjeka/Krk),170,340,RPO,20,6141,4,8,3,86
Rabac,Greenway,Car Transfer,Airport Rijeka (RIjeka/Krk) - Rabac,170,340,RPO,20,6142,4,8,3,87
Rabac,Greenway,Minivan Transfer,Airport Pula - Rabac,120,240,RPO,20,6106,4,9,3,20
Rabac,Greenway,Minivan Transfer,Rabac - Airport Pula,120,240,RPO,20,6105,4,9,3,21
Rabac,Greenway,Minivan Transfer,Rabac - Airport Marco Polo,470,940,RPO,20,6121,4,9,3,74
Rabac,Greenway,Minivan Transfer,Airport Marco Polo - Rabac ,470,940,RPO,20,6122,4,9,3,75
Rabac,Greenway,Minivan Transfer,Rabac - Airport Ronchi dei Legionari (Trst),280,560,RPO,20,6113,4,9,3,76
Rabac,Greenway,Minivan Transfer, Airport Ronchi dei Legionari (Trst) - Rabac,280,560,RPO,20,6114,4,9,3,77
Rabac,Greenway,Minivan Transfer,Rabac - Airport San`t Angelo (Treviso),470,940,RPO,20,6259,4,9,3,80
Rabac,Greenway,Minivan Transfer,Airport San`t Angelo (Treviso) - Rabac,470,940,RPO,20,6260,4,9,3,81
Rabac,Greenway,Minivan Transfer,Rabac - Airport Joze Pucnik (Ljubljana),350,700,RPO,20,6129,4,9,3,82
Rabac,Greenway,Minivan Transfer,Airport Joze Pucnik (Ljubljana) - Rabac,350,700,RPO,20,6130,4,9,3,83
Rabac,Greenway,Minivan Transfer,Rabac - Airport Franjo Tuđman (Zagreb),430,860,RPO,20,6137,4,9,3,84
Rabac,Greenway,Minivan Transfer,Airport Franjo Tuđman (Zagreb) - Rabac,430,860,RPO,20,6138,4,9,3,85
Rabac,Greenway,Minivan Transfer,Rabac - Airport Rijeka (RIjeka/Krk),200,400,RPO,20,6145,4,9,3,86
Rabac,Greenway,Minivan Transfer,Airport Rijeka (RIjeka/Krk) - Rabac,200,400,RPO,20,6146,4,9,3,87';


        $row = explode("\n", $cijene);

        $write_log = array();

        foreach ($row as $ind_row) {

            $ind_entry_data = explode(',', $ind_row);

            $destinacija_naziv = $ind_entry_data[0];
            $partner_naziv = $ind_entry_data[1];
            $transfer_naziv = $ind_entry_data[2];
            $ruta_naziv = $ind_entry_data[3];
            $cijena_one_way = $ind_entry_data[4];
            $cijena_two_way = $ind_entry_data[5];
            $porezni_razred = $ind_entry_data[6];
            $provizija = $ind_entry_data[7];
            $opera_kod = $ind_entry_data[8];
            $destination_id = $ind_entry_data[9];
            $transfer_id = $ind_entry_data[10];
            $partner_id = $ind_entry_data[11];
            $route_id = $ind_entry_data[12];


            if (in_array($destination_id, $this->update_destinations)) {
                if ($cijena_one_way > 0) {
                    $data = array(
                        'price_one_way' => $cijena_one_way * 100,
                        'price_two_way' => $cijena_two_way * 100,
                        'razred' => $porezni_razred,
                        'provizija' => $provizija,
                        'opera_code' => $opera_kod,
                        'destination_id' => $destination_id,
                        'transfer_id' => $transfer_id,
                        'partner_id' => $partner_id,
                        'route_id' => $route_id
                    );

                    $write_log[] = $data;

                }
            }
        }

        if(!empty($write_log)) {

            foreach($write_log as $log){

                \DB::table("route_transfer")
                    ->where('transfer_id',$log['transfer_id'])
                    ->where('partner_id',$log['partner_id'])
                    ->where('route_id',$log['route_id'])
                    ->update(
                            ['price' => $log['price_one_way'],
                            'price_round_trip' => $log['price_two_way'],
                            'tax_level' => $log['razred'],
                            'commission' => $log['provizija'],
                            'opera_package_id' => $log['opera_code']
                        ]
                );
            }

        }
        echo print_r($write_log,true);
        die();
    }


}
