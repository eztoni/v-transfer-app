<div x-data="app">
    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between">
                <span>Partner edit: {{$this->partner->name}}</span>
                <a href="{{route('admin.partners-overview')}}" class="btn btn-link btn-sm">Back to partner overview</a>

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>

    <div class="mt-4">
        <x-ez-card class="h-full mb-4">
            <x-slot name="body">
                <div class="flex justify-between ">
                    <p class="text-xl font-bold">
                        Partner - Information
                    </p>
                </div>



                <div class="form-control">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name :</span>
                        </label>
                        <input wire:model="partner.name" class="input input-bordered"
                               placeholder="Name">
                        @error('partner.name')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone :</span>
                        </label>
                        <input wire:model="partner.phone" class="input input-bordered"
                               placeholder="+385 91 119 9111">
                        @error('partner.phone')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email :</span>
                        </label>
                        <input wire:model="partner.email" class="input input-bordered"
                               placeholder="Email">
                        @error('partner.email')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                </div>

                <div class="form-control" wire:ignore>

                    <label class="label">
                        <span class="label-text">Destinations:</span>
                    </label>

                    <select class="input input-bordered" multiple id="select2">
                        @foreach($destinations as $d)
                            <option
                                {{$this->destinationPartners->contains('id',$d->id) ? 'selected' : ''}} value="{{$d->id}}">{{Str::ucfirst($d->name)}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('selectedDestinations')
                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                @enderror

                <button class="btn btn-sm btn-success ml-auto mt-4 bottom-4 right-4"
                        wire:click="savePartnerData">Update
                </button>


            </x-slot>
        </x-ez-card>
    </div>



    <script>
        function app() {
            return {
                open: false,
                init() {

                    $('#select2').select2(
                        {
                            multiple: true,
                            closeOnSelect: false
                        }
                    ).on('change', function (e) {
                        @this.
                        set('selectedDestinations', $('#select2').select2("val"))
                    }).on('select2:unselecting', function (e) {
                        if ($(e.params.args.data.element).attr('locked')) {
                            e.preventDefault();
                        }
                    });

                }
            }
        }
    </script>


</div>
