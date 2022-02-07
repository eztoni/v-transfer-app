<div x-data="app()" x-init="initFlatpickr">

        <div class="flex flex-row-reverse  mb-4">
            <a href="{{ route('radnici') }}" ><button class="btn  "> Pregled Radnika</button></a>
        </div>

    <x-ez-card>
        <x-slot name="body">

            <label class="label">
                <span class="label-text">Ime:</span>
            </label>
            <input wire:model="editData.name" class="input input-bordered" placeholder="Ime">
            @error('editData.name')<x-input-alert type='warning'>Polje za ime je obavezno.</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Prezime:</span>
            </label>
            <input wire:model="editData.surname" class="input input-bordered" placeholder="Prezime">
            @error('editData.surname')<x-input-alert type='warning'>Polje za prezime je obavezno.</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Email:</span>
            </label>
            <input wire:model="editData.email" class="input input-bordered" type="email" placeholder="Email">
            @error('editData.email')<x-input-alert type='warning'>Polje za email je obavezno.</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Broj Telefona:</span>
            </label>
            <input wire:model="editData.phone_number" class="input input-bordered" placeholder="Broj Telefona">
            @error('editData.phone_number')<x-input-alert type='warning'>Polje za broj telefona je obavezno.</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Grad:</span>
            </label>
            <input wire:model="editData.city" class="input input-bordered" placeholder="Grad">
            @error('editData.city')<x-input-alert type='warning'>Polje za grad je obavezno.</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Datum zaposlenja:</span>
            </label>
            <input x-ref="picker" wire:model="editData.employment_date" class="input input-bordered " readonly placeholder="Datum zaposlenja">
            @error('editData.employment_date') <x-input-alert type='warning'>Polje obavezno</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">OIB:</span>
            </label>
            <input wire:model="editData.OIB" class="input input-bordered" placeholder="OIB">
            @error('editData.OIB')<x-input-alert type='warning'>Polje za OIB je obavezno i mora imat 11 znamenki.</x-input-alert>@enderror

            <div class="mt-5 ml-auto">
                <button wire:click.prevent="saveWorkerData()" class="btn btn-primary">Spremi</button>
            </div>



        </x-slot>
    </x-ez-card>

</div>

<script>
    function app() {
        return {
            initFlatpickr() {
                const fp = flatpickr(this.$refs.picker, {
                    disableMobile: "true"
                });
            }
        }
    }
</script>
