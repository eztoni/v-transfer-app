<div x-data="extraImages()">

    <x-card cardClasses="px-0 pb-0">
        <div wire:ignore>
            <p class="mb-4">Upload up to <span class="ds-badge">{{$this->model::MAX_IMAGES}} images</span>  for your extra. Our app optimizes  images before storing them. <br>Maximum image size is
                <span class="ds-badge">3 MB</span>.
                We suggest using extra like <a class="link text-blue-600" href="https://tinypng.com">TinyPNG</a> for optimizing your images before storing them, so your booking engine loads faster!
            </p>
            <form id="dropzone"
                  class="dropzone bg-gradient-to-r  rounded-tl-none   rounded-lg  rounded-tr-none  gradient from-blue-400 to-blue-500 border-none text-white"
                  action="{{route('admin.upload-images')}}">
                <input type="hidden" name="model_id" value="{{$this->model->id}}">
                <input type="hidden" name="model" value="{{ class_basename($this->model) }}">
                <input type="hidden" name="mediaCollectionName" value="{{$this->mediaCollectionName}}">
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
        </div>
    </x-card>

    <x-card cardClasses="mt-4" title="Images: {{$this->model->getMedia($this->mediaCollectionName)->count()}}/{{$this->model::MAX_IMAGES}}">
        <div class="grid  lg:grid-cols-4 sm:grid-cols-3 grid-cols-1 gap-4">
            @forelse($this->model->getMedia($this->mediaCollectionName) as $media )
                <div class=" group block   " >
                    <div class="   relative">
                        @if($media->hasCustomProperty($this->model::IMAGE_PRIMARY_PROPERTY))
                            <div class="ds-badge ds-badge-success gap-2 bg-success text-neutral-content absolute bottom-5 left-5">
                                Primary
                            </div>
                        @endif
                        <button wire:click="delete({{$media->id}})" class="ds-badge ds-badge-error group-hover:opacity-100 opacity-0 bg-error text-neutral-content absolute top-2  right-2">
                            X
                        </button>
                        <img
                            wire:key="{{Str::random()}}"
                            src="{{$media->getFullUrl('thumb')}}"
                            class="group-hover:rounded-b-none rounded h-44 object-cover  border border-gray-200 w-full">
                    </div >
                    <div class="ds-btn-group flex group-hover:opacity-100 opacity-0 z-20 relative duration-150  transition-opacity">
                        <button wire:click="moveLeft({{$media->id}})" class="ds-btn ds-btn-accent ds-btn-xs ds-rounded-t-none" ><-</button>
                        <button wire:click="makePrimary({{$media->id}})" class=" ds-btn ds-btn-primary flex-grow ds-btn-xs" >Make primary</button>
                        <button wire:click="moveRight({{$media->id}})"  class=" ds-btn ds-btn-accent ds-btn-xs ds-rounded-t-none" >-></button>
                    </div>

                </div>
            @empty
                <div class="col-span-10">
                    <x-input-alert  type='warning'>Please upload at least one image!</x-input-alert>

                </div>

            @endforelse
        </div>
    </x-card>

    @include('packages.dropzone')
    <script>

        function extraImages() {
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
