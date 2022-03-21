<div>
    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between">
                <span>  Upload Images:</span>
                <a href="{{route('transfer-overview')}}" class="btn btn-link btn-sm">Back to transfer overview</a>

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>


    @livewire('image-gallery-uploader',['id' => $transfer->id,'model' => $transfer,'mediaCollectionName' => 'transferImages'])
</div>
