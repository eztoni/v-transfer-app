<div>

    <x-ez-card class="mb-5">
        <x-slot name="body">
            <div class="flex justify-between">
                <span class="pt-1">Upload Images:</span>
                <a href="{{route('extras-overview')}}" class="btn btn-link btn-sm">Back to extras overview</a>
            </div>
        </x-slot>
    </x-ez-card>


   @livewire('image-gallery-uploader',['id' => $extra->id,'model' => $extra])
</div>
