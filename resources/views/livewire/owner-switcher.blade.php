

<div>
    <x-dropdown>
        <x-slot name="trigger">
            <x-button icon="library" :label="$userOwnerName" flat />
        </x-slot>
        @foreach($owners as $owner)
            <x-dropdown.item   class="{{$owner->id !== \Auth::user()->owner_id?:'bg-primary-100'}}" label="    #{{$owner->id}} - {{$owner->name}}" wire:click="changeOwner({{$owner->id}})" />
        @endforeach
    </x-dropdown>
</div>
