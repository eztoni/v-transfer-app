<div >

    <div class="flex gap-4 w-full">
        <x-native-select
            label="Type:"
            placeholder="Select text level"
            :options="['CONFIRMATION', 'MODIFY','ATTACHMENT_VOUCHER','ATTACHMENT_CONFIRMATION','ATTACHMENT_CANCELLATION']"
            wire:model="mailType"
        />
        <x-select
            label="Reservation"
            option-label="name"
            option-value="id"
            :searchable="true"
            class="flex-grow"
            :clearable="false"
            min-items-for-search="2"
            wire:model="resId"
            :options="$this->reservationsForSelect"
        />
        <x-button
                wire:click="render"
                  target="_blank"
                  label="REFRESH" />
        @if($resId)

        <x-button href="{{route('res-mail-render',['type'=>$mailType,'id'=>$resId])}}"
                  positive
                  target="_blank"
                  label="Open in a new tab" />
            @endif


    </div>

@if($resId)
    <iframe src='{{route('res-mail-render',['type'=>$mailType,'id'=>$resId])}}' frameborder="0" class="w-full h-screen my-8 border border-primary-500 border-2 rounded-lg" wire:key="{{Str::random()}}">

    </iframe>
    @endif

</div>
