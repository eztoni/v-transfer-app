
<div class="form-control {{$parentDivClasses}}">
    <label class="label {{$labelClasses}}">
        <span class="label-text {{$labelTextClasses}}">{{$label}}</span>
    </label>
    <input  {{$attributes}} placeholder="" wire:model="{{$model}}"   {{$attributes->merge(['class'=>' input input-bordered'])}}>
    @error($errorString)
    <x-input-alert type='warning'>{{$message}}</x-input-alert>
    @enderror
</div>
