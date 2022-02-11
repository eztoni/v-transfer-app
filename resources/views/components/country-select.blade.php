@props(['livewireModel'=>'','placeholder'=>'Select a country'])

<div>
    <select {{ $attributes->merge(['class' => 'select select-bordered']) }} wire:model="{{$livewireModel}}"  >
        <option value="">{{$placeholder}}</option>
        @foreach(\App\Models\Country::all(['id','nicename']) as $country )
            <option value="{{$country->id}}">{{$country->nicename}}</option>
        @endforeach
    </select>
</div>
