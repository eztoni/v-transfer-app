<div x-data="{open: false,selectedLanguage: 'en'}">

    <x-card title="Companies">
        <x-slot name="action">
            <x-button wire:click="addCompany()" primary label="Add Company"/>
        </x-slot>


        <x-input label="Find company" wire:model="search"></x-input>

        <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
            <thead>
            <tr>
                <th>#Id</th>
                <th>Name</th>
                <th class="text-right pr-4">Update</th>
                <th class="text-right pr-4">Delete</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($companies as $company)
                <tr>
                    <th>{{ $company->id }}</th>
                    <th>{{ $company->name }}</th>
                    <td class="text-right">
                        <x-button
                            wire:click="updateCompany({{$company->id}})"
                            rounded
                            primary icon="pencil"
                            target="_blank"
                        />
                    </td>
                    <td class="text-right">
                        <x-button
                            wire:click="openSoftDeleteModal({{$company->id}})"
                            rounded
                            rose icon="trash"
                            target="_blank"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="999">
                        <div class="alert alert-warning">
                            <div class="flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     class="w-6 h-6 mx-2 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <label>No defined companies</label>
                            </div>
                        </div>
                    </TD>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{$companies->links()}}
    </x-card>

    <x-modal.card blur wire:model.defer="companyModal" title="{{$this->company->id > 0 ? 'Update':'Add'}} Company">

        <x-input label="Name:" wire:model.defer="company.name" />

        <x-native-select
            label="Country:"
            :options="\App\Models\Country::all(['id','nicename'])->mapWithKeys(fn ($model) => [$model->id => $model->nicename])->toArray()"
            wire:model="company.country_id"
        />

        <x-input label="Email:" wire:model.defer="company.email" />
        <x-input label="Contact:" wire:model.defer="company.contact" />
        <x-input label="Zip:" wire:model.defer="company.zip" />
        <x-input label="City:" wire:model.defer="company.city" />
        <x-input label="Website:" wire:model.defer="company.website" />

        <x-errors />

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="closeCompanyModal" label="Close" rose/>
                <x-button wire:click="saveCompanyData" label="Save" positive/>
            </div>

        </x-slot>
    </x-modal.card>

    <x-modal.card blur wire:model.defer="softDeleteModal" title="Delete Company - {{$this->company->name}}">

        <p>You are about to delete <b>{{$this->company->name}}</b> are you sure you want to do that?</p>
        <p class="text-rose-500">CAREFUL - This action will delete the company!</p>

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="closeSoftDeleteModal()" label="Close" rose/>
                <x-button wire:click="softDelete()" label="Delete" positive/>
            </div>

        </x-slot>

    </x-modal.card>

</div>

