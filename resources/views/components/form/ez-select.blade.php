
<div class="form-control {{$parentDivClasses}}">
    <label class="label {{$labelClasses}}">
        <span class="label-text {{$labelTextClasses}}">{{$label}}</span>
    </label>
    <select   {{$attributes}} wire:model="{{$model}}"
        {{$attributes->class(['select-sm p-1'=>!empty($sm)])->merge(['class'=>' select select-bordered'])}}
    >
        @if($showEmptyValue)
            <option value="">Please select a {{\Str::lower($label)}}</option>
        @endif

        @foreach($items as $id => $text)
            <option value="{{$id}}">{{$text}}</option>
        @endforeach
    </select>
    @error($errorString)
    <x-input-alert type='warning'>{{$message}}</x-input-alert>
    @enderror
</div>
