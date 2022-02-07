<div x-data="app()">
    @if(!empty($partner->id))
    <div class="flex flex-row-reverse  mb-4">
        <a href="{{ route('poslovi',['partner'=>$partner->id]) }}" class="ml-4"><button class="btn btn-primary "> Pregled Poslova</button></a>

        <a href="{{ route('partneri') }}" ><button class="btn  "> Pregled Partnera</button></a>
    </div>
    @endif
    <x-ez-card>
        <x-slot name="body">

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Ime Poslovanja :</span>
                </label>
                <input wire:model.debounce.500ms="editData.business_name" class="input input-bordered"
                       placeholder="Ime Poslovanja">
                @error('editData.business_name')
                <x-input-alert type='warning'>Polje za ime poslovanja je obavezno.</x-input-alert>@enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Kontakt Ime:</span>
                </label>
                <input wire:model.debounce.500ms="editData.contact_name" class="input input-bordered"
                       placeholder="Kontakt Ime">
                @error('editData.contact_name')
                <x-input-alert type='warning'>Polje za kontakt ime je obavezno.</x-input-alert>@enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Kontakt Broj:</span>
                </label>
                <input wire:model.debounce.500ms="editData.contact_number" class="input input-bordered"
                       placeholder="Kontakt Broj">
                @error('editData.contact_number')<x-input-alert type='warning'>Polje za kontakt broj je obavezno.</x-input-alert>@enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input wire:model.debounce.500ms="editData.email" class="input input-bordered"
                       placeholder="Email">
                @error('editData.email')<x-input-alert type='warning'>Polje za email je obavezno i mora imati točan email format.</x-input-alert>@enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">OIB:</span>
                </label>
                <input wire:model="editData.OIB" class="input input-bordered" placeholder="OIB">
                @error('editData.OIB')<x-input-alert type='warning'>Polje za OIB je obavezno i treba sadržavati 11 znamenki.</x-input-alert>@enderror
            </div>


            <div class="form-control" wire:ignore>
                <label class="label">
                    <span class="label-text">Pocetak Ugovora:</span>
                </label>

                <input x-init="flatpickr($el, {disableMobile: 'true' });" readonly wire:model.debounce.300ms="editData.beginning_of_contract"
                       class="input input-bordered" placeholder="Pocetak Ugovora:">
            </div>

            <div>
                @error('editData.beginning_of_contract')
                <x-input-alert type='warning'>Polje za pocetak ugovora je obavezno.</x-input-alert>@enderror
            </div>


            <div class="form-control" wire:ignore>
                <label class="label">
                    <span class="label-text">Završetak Ugovora:</span>
                </label>

                <input x-init="flatpickr($el, {disableMobile: 'true' });" readonly wire:model.debounce.300ms="editData.end_of_contract"
                       class="input input-bordered" placeholder="Završetak Ugovora:">
            </div>

            <div>
                @error('editData.end_of_contract')
                <x-input-alert type='warning'>Polje za završetak ugovora je obavezno.</x-input-alert>@enderror
            </div>

            <div class="mt-5 ml-auto">
                <button wire:click.prevent="savePartnerData()" class="btn btn-primary">Spremi</button>
            </div>

        </x-slot>
    </x-ez-card>
</div>

<script>
    function app() {
        return {
            init() {


            }
        }
    }
</script>
