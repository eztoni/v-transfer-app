<div>

    <x-card title="Languages">
        <x-slot name="action">
            <x-button wire:click="addLanguage()" primary label="Add Language"/>
        </x-slot>


        <x-input label="Find language" wire:model="search"></x-input>

        <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
            <thead>
            <tr>
                <th>#Id</th>
                <th>Name</th>
                <th>ISO 639-1</th>
                <th class="text-right pr-4">Update</th>
                <th class="text-right pr-4">Delete</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($languages as $l)
                <tr>
                    <th>{{ $l->id }}</th>
                    <th>{{ $l->name }}</th>
                    <th>{{ $l->language_code }}</th>
                    <td class="text-right">
                        <x-button
                            wire:click="updateLanguage({{$l->id}})"
                            rounded
                            primary icon="pencil"
                            target="_blank"
                        />
                    </td>
                    <td class="text-right">
                        <x-button
                            wire:click="openSoftDeleteModal({{$l->id}})"
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
                                <label>No defined languages</label>
                            </div>
                        </div>
                    </TD>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{$languages->links()}}
    </x-card>

    <x-modal.card blur wire:model.defer="languageModal" title="{{$this->language->id > 0 ? 'Update':'Add'}} Language">
        <div class="form-control mb-4">
            <x-input label="Name:" wire:model.defer="language.name" />
        </div>

        <div class="form-control mb-4">
            <x-input label="Language Code:" wire:model.defer="language.language_code" placeholder="ISO 639-1 code"/>
        </div>

        <x-errors />

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="closeLanguageModal" label="Close" rose/>
                <x-button wire:click="saveLanguageData" label="Save" positive/>
            </div>

        </x-slot>
    </x-modal.card>

    <x-modal.card blur wire:model.defer="softDeleteModal" title="Delete Exclude - {{$this->language->name}}">

        <p>You are about to delete <b>{{$this->language->name}}</b> are you sure you want to do that?</p>
        <p class="text-rose-500">CAREFUL - This action will delete the exclude!</p>

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="closeSoftDeleteModal()" label="Close" rose/>
                <x-button wire:click="softDelete()" label="Delete" positive/>
            </div>

        </x-slot>

    </x-modal.card>

</div>






