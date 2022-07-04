
    <div>
        <x-dropdown>
            <x-slot name="trigger">
                <x-button icon="library" :label="$userCompanyName" flat />
            </x-slot>
            @foreach($companies as $company)
                <x-dropdown.item   class="{{$company->id !== \Auth::user()->company_id?:'bg-primary-100'}}" label="    #{{$company->id}} - {{$company->name}}" wire:click="changeCompany({{$company->id}})" />
            @endforeach
        </x-dropdown>
    </div>



