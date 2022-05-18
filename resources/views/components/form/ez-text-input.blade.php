<div class="form-control {{$parentDivClasses}}">
    <label class="label {{$labelClasses}}">
        <span class="label-text {{$labelTextClasses}}">{{$label}}</span>
    </label>
    <input   placeholder=""
            @if(!empty($model)) wire:model="{{$model}}" @endif
            @if(!empty($value)) value="{{$value}}" @endif

             {{$attributes->class(['input-sm'=>!empty($sm)])->merge(['class'=>' input input-bordered '])}} {{$attributes}}>
    @error($errorString)
    <x-input-alert type='warning'>{{$message}}</x-input-alert>
    @enderror
</div>
