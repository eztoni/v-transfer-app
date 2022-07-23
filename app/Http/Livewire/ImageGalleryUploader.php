<?php

namespace App\Http\Livewire;

use App\Models\Extra;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use WireUi\Traits\Actions;

class ImageGalleryUploader extends Component
{
use Actions;

    public $modelId;
    public $model;
    public $mediaCollectionName;

    public function mount($id)
    {
        $this->modelId = $id;
    }

    public function delete($id){
        $images = $this->model::findOrFail($this->modelId)->getMedia($this->mediaCollectionName);

        if ($image = $images->where('id', '=', $id)->first()) {
            $image->delete();
        }

    }

    public function makePrimary($id)
    {
        $images = $this->model::findOrFail($this->modelId)->getMedia($this->mediaCollectionName);


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
        $extra = $this->model::findOrFail($this->modelId);
        $images = $extra->getMedia($this->mediaCollectionName);

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
        $extra = $this->model::findOrFail($this->modelId);
        $images = $extra->getMedia($this->mediaCollectionName);

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
        $this->model = $this->model::findOrFail($this->modelId);

        return view('livewire.image-gallery-uploader');
    }
}
