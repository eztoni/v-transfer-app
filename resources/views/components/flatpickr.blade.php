<div>
    @if ($label)
        <x-dynamic-component
            :component="WireUi::component('label')"
            class="mb-1"
            :label="$label"
            :has-error="$errors->has($name)"
            :for="$id"
        />
    @endif
    <div  class="shadow-sm ">
        <input x-init="
                        flatpickr($el, {
                        disableMobile: 'true',
                        minDate:'{{$minDate}}',
                        dateFormat:'{{$dateFormat}}',
                        noCalendar:'{{$noCalendar}}',
                        enableTime: {{$enableTime?'true':'false'}},
                        defaultDate:'{{$defaultDate}}',
                        time_24hr: {{$time24?'true':'false'}},

                        @if($enableTime && !$noCalendar && $attributes->has('wire:model.defer'))
                                    onClose: function(selectedDates, dateStr, instance){
                                          @this.set('{{$name}}',dateStr)
                                       }
                        @endif

                        });
                        " readonly
            {{ $attributes->class([
             $defaultClasses(),
             $errorClasses() =>  $errors->has($name),
             $colorClasses() => !$errors->has($name),
            ]) }}
        >
    </div>

    @if ($hint)
        <label @if ($id) for="{{ $id }}" @endif class="mt-2 text-sm text-secondary-500 dark:text-secondary-400">
            {{ $hint }}
        </label>
    @endif

    @if ($name)
        <x-dynamic-component
            :component="WireUi::component('error')"
            :name="$name"
        />
    @endif
</div>
