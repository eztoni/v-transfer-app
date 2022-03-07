<div x-data="serviceImages()">

    <x-ez-card>
        <x-slot name="title" class="p-6 py-4 mb-0">
            <div class="flex justify-between">
                <span class="pt-1">Upload Images:</span>
                <a href="{{route('extras-overview')}}" class="btn btn-link btn-sm">Back to extras overview</a>
            </div>
        </x-slot>
        <x-slot name="body" class="p-0" wire:ignore>
            <p class="px-6 mb-4">Upload up to <span class="badge">{{\App\Models\Extra::MAX_IMAGES}} images</span>  for your service. Our app optimizes  images before storing them. <br>Maximum image size is
                <span class="badge">3 MB</span>.
                We suggest using service like <a class="link text-blue-600" href="https://tinypng.com">TinyPNG</a> for optimizing your images before storing them, so your booking engine loads faster!
            </p>
            <form id="dropzone"
                  class="dropzone bg-gradient-to-r  rounded-tl-none   rounded-lg  rounded-tr-none  gradient from-blue-400 to-blue-500 border-none text-white"
                  action="{{route('admin.upload-service-images')}}">
                <input type="hidden" name="service_id" value="{{$service->id}}">
                <div class="dz-default dz-message">
                    <div class="dz-icon">
                        <i class="fas fa-upload text-6xl mb-4"></i>
                    </div>
                    <div>
                        <span class="dz-text">Drop files to upload</span>
                        <p class="text-sm  text-muted">or click to pick manually</p>
                    </div>
                </div>
            </form>

        </x-slot>
    </x-ez-card>

    <x-ez-card >

        <x-slot name="title">
            Images: {{$service->getMedia('serviceImages')->count()}}/{{\App\Models\Service::MAX_IMAGES}}
        </x-slot>

        <x-slot name="body" class="">
            <div class="grid  lg:grid-cols-4 sm:grid-cols-3 grid-cols-1 gap-4">
                @foreach($service->getMedia('serviceImages') as $media )
                    <div class=" group block py-4  " >
                        <div class="   relative">
                            @if($media->hasCustomProperty(\App\Models\Service::IMAGE_PRIMARY_PROPERTY))
                                <div class="badge badge-success gap-2 bg-success text-neutral-content absolute bottom-5 left-5">
                                    Primary
                                </div>
                            @endif
                            <button wire:click="delete({{$media->id}})" class="badge badge-error group-hover:opacity-100 opacity-0 bg-error text-neutral-content absolute top-2  right-2">
                                X
                            </button>
                            <img src="{{$media->getFullUrl('thumb')}}" class="group-hover:rounded-b-none rounded h-44 object-cover  border border-gray-200 w-full">
                        </div >
                        <div class="btn-group flex group-hover:opacity-100 opacity-0 z-20 relative duration-150  transition-opacity">
                            <button wire:click="moveLeft({{$media->id}})" class=" btn btn-accent btn-xs rounded-t-none" ><-</button>
                            <button wire:click="makePrimary({{$media->id}})" class=" btn btn-primary flex-grow btn-xs" >Make primary</button>
                            <button wire:click="moveRight({{$media->id}})"  class=" btn btn-accent btn-xs rounded-t-none" >-></button>
                        </div>

                    </div>
                @endforeach
            </div>
        </x-slot>
    </x-ez-card>
    @include('packages.dropzone')
    <script>

        function serviceImages() {
            return {
                init() {
                    let myDropzone = new Dropzone("#dropzone", {
                        headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}
                    });
                    myDropzone.on("queuecomplete", file => {
                        this.$wire.render()

                    });
                }
            }
        }
    </script>
</div>
