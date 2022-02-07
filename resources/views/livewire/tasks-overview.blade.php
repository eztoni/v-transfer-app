<div x-data="app()">

    <div class="flex justify-between mb-4 flex-wrap md:flex-nowrap">
        <h1 class="text-2xl">Poslovi za partnera: {{$partner->business_name}}</h1>
        <div>
        <button class="ml-4 btn mb-2 btn-success" wire:click="addNewTask"> Dodaj Posao</button>

        <a href="{{ route('uredi-partnera',['id'=>$partner->id]) }}">
            <button class="btn mb-2 ml-4 btn-primary"> Uredi Partnera</button>
        </a>
        <a href="{{ route('partneri') }}">
            <button class="btn mb-2  "> Pregled Partnera</button>
        </a>
        </div>
    </div>

    @if($showForm)
        <x-ez-card class="mb-4">
            <x-slot name="body">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Ime:</span>
                    </label>
                    <input wire:model.debounce.300ms="editData.name" class="input input-bordered" placeholder="Ime">
                    @error('editData.name')
                    <x-input-alert type='warning'>Polje za ime je obavezno.</x-input-alert>@enderror
                </div>

                <div class="form-control" wire:ignore>
                    <label class="label">
                        <span class="label-text">Datum Početka:</span>
                    </label>
                    <input x-init="flatpickr($el, {disableMobile: 'true'});" readonly wire:model.debounce.300ms="editData.start_date"
                           class="input input-bordered" placeholder="Datum Početka:">
                </div>

                <div>
                    @error('editData.start_date')
                    <x-input-alert type='warning'>Polje za datum početka je obavezno.</x-input-alert>@enderror
                </div>


                <div class="form-control" wire:ignore>
                    <label class="label">
                        <span class="label-text">Datum Završetka:</span>
                    </label>

                    <input x-init="flatpickr($el, {disableMobile: 'true' });" readonly wire:model.debounce.300ms="editData.end_date"
                           class="input input-bordered" placeholder="Datum Završetka:">
                </div>

                <div>
                    @error('editData.end_date')
                    <x-input-alert type='warning'>Polje za datum završetka je obavezno.</x-input-alert>@enderror
                </div>

                <div class="flex sm:gap-4 flex-wrap sm:flex-nowrap">
                    <div class="form-control  ">
                        <label class="label">
                            Posao se ponavlja svako:
                        </label>
                        <input wire:model.debounce.300ms="editData.recurring_value" class="input input-bordered"
                               placeholder="Unesite broj">
                        @error('editData.recurring_value')
                        <x-input-alert type='warning'>Polje za ponavljanje posla je obavezno i mora biti veće od 0.</x-input-alert>@enderror
                    </div>
                    <div class="form-control ">
                        <label class="label">
                            Jedinica
                        </label>
                        <select wire:model.debounce.300ms="editData.recurring_metrics"
                                class="  input input-bordered">

                            <option value="day">Dana</option>
                            <option value="month">Mjeseci</option>
                        </select>
                        @error('editData.recurring_metrics')
                        <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                    </div>

                </div>
                <div class="flex flex-row-reverse my-4">
                    <button wire:click.prevent="saveOrUpdateTask()" class="btn btn-primary ml-4">Spremi</button>

                    <button wire:click.prevent="closeForm()" class="btn btn-warning">Zatvori</button>
                </div>

            </x-slot>
        </x-ez-card>
    @endif

    @forelse ($partner->tasks as $task)
        <x-ez-card class="mb-4">
            <x-slot name="title">
                <b>{{$task->name}}</b>
            </x-slot>
            <x-slot name="body" >

                <div class="mt-2 flex flex-wrap justify-between bg-base-200 rounded-box p-4">
                    <div class="">
                        <div class=" p-0">
                            <p class="">Ponavljanje svako:</p>
                            <p
                                class="badge  badge-outline">{{$task->recurring_value}} {{$task->recurring_metrics}}</p>
                        </div>
                    </div>
                    <div class="">
                        <div class=" p-0">
                            <p class="">Pocetak Posla:</p>
                            <p class="badge badge-outline">{{$task->start_date}}</p>
                        </div>
                    </div>
                    <div class="">
                        <div class=" p-0">
                            <p class="">Završetak Posla:</p>
                            <p class="badge badge-outline">{{$task->end_date}}</p>
                        </div>
                    </div>
                    <div class="">
                        <div class=" p-0">
                            <p class="">Datum sljedećeg posla:</p>
                            <p class="badge badge-outline">{{date('d.m.Y', strtotime($task->next_date))}}</p>
                        </div>
                    </div>
                    <div class="">
                        <div class=" p-0">
                            <p class="">Obavjesti me</p>
                            <div class="flex justify-between">
                                <input wire:model="daysBefore.{{$task->id}}" class="input input-primary input-sm w-32" type="number" placeholder="dana">
                                <button wire:click="updateTaskAlarmValue({{$task->id}})" class="btn btn-sm">Ažuriraj</button>
                            </div>
                            <p class="">dana prije!</p>

                        </div>
                    </div>
                    <div class="">
                        <div class=" pt-6">
                            <button x-on:click="showModal({{$task->id}})" class="btn btn-sm btn-warning ">Izbriši</button>
                        </div>
                    </div>
                    <div class="">
                        <div class=" pt-6">
                                <button type="button"  wire:click.prevent="editTask({{$task->id}})" x-on:click="scrollToTop"
                                        class="btn btn-sm btn-primary">Uredi Posao
                                </button>
                        </div>
                    </div>

                    @error('reccError'.$task->id)<span class="alert alert-error" >{{ $message }}</span>@enderror

                </div>


                <x-ez-modal id="modal" >
                    Potvrdi brisanje?
                    <hr class="my-4">
                    <div class="flex justify-between">
                        <button x-on:click="closeModal()" for="dashboardModal" class="btn btn-sm ">Zatvori</button>
                        <button x-on:click="deleteModel()" for="dashboardModal" class="btn btn-sm ">Potvrdi</button>
                    </div>

                </x-ez-modal>
            </x-slot>
        </x-ez-card>
    @empty
        <x-ez-card>
            <x-slot name="body">
                <h1>Nema dodanih poslova za odabranog partnera.</h1>
            </x-slot>
        </x-ez-card>
    @endforelse
</div>

<script>
    function app() {
        return {
            init() {
            },
            scrollToTop() {
                document.querySelector('main').scrollTo(0, 0);
            },
            deleteId:'',
            showModal(id){
                this.deleteId=id
                this.$refs.modal.checked = true
            },
            closeModal(){
                this.$refs.modal.checked = false
            },
            deleteModel(){
                this.$wire.softDelete(this.deleteId)
                this.closeModal()
                this.deleteId=''
            },
        }
    }
</script>

