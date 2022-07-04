
<div>
    <x-dropdown>
        <x-slot name="trigger">
            <x-button icon="library" :label="$userDestinationName" flat />
        </x-slot>
        @foreach($destinations as $destination)
            <x-dropdown.item   class="{{$destination->id !== \Auth::user()->destination_id ?:'bg-primary-100'}}" label="    #{{$destination->id}} - {{$destination->name}}" wire:click="changeDestination({{$destination->id}})" />
        @endforeach
    </x-dropdown>
</div>
