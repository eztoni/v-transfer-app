<?php

namespace App\Http\Livewire;

use App\Models\Extra;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageGalleryUploader extends Component
{

    public $extraId;
    public $model;

    public function mount($id)
    {


        $this->extraId = $id;
    }

    public function delete($id){
        $images = $this->model::findOrFail($this->extraId)->getMedia('extraImages');

        if ($image = $images->where('id', '=', $id)->first()) {
            $image->delete();
        }

    }

    public function makePrimary($id)
    {
        $images = $this->model::findOrFail($this->extraId)->getMedia('extraImages');


        if ($image = $images->where('id', '=', $id)->first()) {
            foreach ($images as $i) {
                if ($i->hasCustomProperty($this->model::IMAGE_PRIMARY_PROPERTY)) {
                    $i->forgetCustomProperty($this->model::IMAGE_PRIMARY_PROPERTY)->save();
                }
            }
            $image->setCustomProperty($this->model::IMAGE_PRIMARY_PROPERTY, true)->save();
        }

    }


    public function moveRight($id)
    {
        $extra = $this->model::findOrFail($this->extraId);
        $images = $extra->getMedia('extraImages');

        $ids = $images->pluck('id')->toArray();

        $insertId = [$id];
        if (($key = array_search($id, $ids)) !== false) {
            array_splice($ids, $key, 1);

            array_splice($ids, $key + 1, 0, $insertId);
            Media::setNewOrder($ids);
        }
    }

    public function moveLeft($id)
    {
        $extra = $this->model::findOrFail($this->extraId);
        $images = $extra->getMedia('extraImages');

        $ids = $images->pluck('id')->toArray();
        $insertId = [$id];
        if (($key = array_search($id, $ids)) !== false) {
            array_splice($ids, $key, 1);
            array_splice($ids, $key - 1, 0, $insertId);
            Media::setNewOrder($ids);
        }
    }

    public function render()
    {
        $extra = $this->model::findOrFail($this->extraId);
        return view('livewire.image-gallery-uploader',compact('extra'));
    }
}
