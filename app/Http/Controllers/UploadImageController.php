<?php

namespace App\Http\Controllers;


use App\Models\Extra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class UploadImageController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();

        $service = Extra::find($request->extra_id);

        $validation = Validator::make($input, [
            'file' => 'dimensions:max_width=2248,max_height=1800|mimes:jpeg,png,jpg,webp|max:3000',
        ]);

        if ($validation->fails())
            return Response::make($validation->errors()->first(), 400);

        $media = $service->getMedia('extraImages');

        if($media->count() >= 12){
            return Response::make('You have reach a maximum of '. Extra::MAX_IMAGES .' images for this service', 400);
        }

        $path = Storage::putFile('tempImages',  $request->file('file'));

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->setTimeout(4)->optimize(Storage::path($path));
        $service->addMedia(Storage::path($path))->toMediaCollection('extraImages');



        return Response::json('success', 200);
    }
}
