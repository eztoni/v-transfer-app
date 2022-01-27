<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    //

    public function show(Request  $request){

        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        $fileName = 'ez.log';
        $logs = [];
        if(Storage::disk('logs')->exists('ez.log')){

            $content = file_get_contents(storage_path('logs/' . $fileName));
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $match) {
                $logs[] = [
                    'date' => $match['date'],
                    'env' => $match['env'],
                    'type' => $match['type'],
                    'message' => trim($match['message'])
                ];
            }
        }
        return view('log-view',['logs' => $logs]);
    }
}
