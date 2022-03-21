<?php

namespace App\Http\Controllers;


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

        $model = 'App\\Models\\'. $request->model;
        $model = $model::find($request->model_id);

        $validation = Validator::make($input, [
            'file' => 'dimensions:max_width=2248,max_height=1800|mimes:jpeg,png,jpg,webp|max:3000',
        ]);

        if ($validation->fails())
            return Response::make($validation->errors()->first(), 400);

        $media = $model->getMedia($request->mediaCollectionName);

        if($media->count() >= 12){
            return Response::make('You have reach a maximum of '. Extra::MAX_IMAGES .' images for this service', 400);
        }

        $path = Storage::putFile('tempImages',  $request->file('file'));

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->setTimeout(4)->optimize(Storage::path($path));
        $model->addMedia(Storage::path($path))->toMediaCollection($request->mediaCollectionName);



        return Response::json('success', 200);
    }
}
