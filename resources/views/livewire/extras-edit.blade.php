<div>

    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex w-full justify-between">
                <span>  Upload Images:</span>
                <a href="{{route('extras-overview')}}" class="btn btn-link  btn-sm">Back to extras overview</a>

            </div>
          </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>


   @livewire('image-gallery-uploader',['id' => $extra->id,'model' => $extra,'mediaCollectionName' => 'extraImages'])
</div>
