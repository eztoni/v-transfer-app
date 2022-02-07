<div>

    <div class=" grid-cols-1  gap-y-4 md:grid-cols-6 md:gap-4 grid">
        <div class="col-span-2">
            <x-ez-card>
                <x-slot name="title" class="flex justify-between border-b border-base-300 pb-4">
                    <p>Partneri</p>
                    <button class="btn btn-sm btn-success btn-circle">
                        <x-icons.plus></x-icons.plus>

                    </button>
                </x-slot>
                <x-slot name="body">
                    <ul class="menu   bg-base-100 ">
                        @if(!empty($partners))
                            @foreach($partners as $partner)
                                <li class="@php if($partner->id === $chosenPartnerId){echo 'border-l-4 border-accent bg-base-200 rounded';}@endphp">
                                    <a class="p-0" wire:click.prevent="selectPartner({{$partner->id}})">
                                        {{$partner->business_name}}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <p>Nema partnera</p>
                        @endif
                    </ul>
                    {{ $partners->links() }}

                </x-slot>

            </x-ez-card>
        </div>
        <div class="col-span-4">
            <x-ez-card>
                <x-slot name="title" class="flex justify-between border-b border-base-300 pb-4">
                    <p>Poslovi</p>
                </x-slot>
                <x-slot name="body">
                    @if(!empty($partnerTasks[$chosenPartnerId]))
                    @foreach($partnerTasks[$chosenPartnerId] as $task)
                        <span>{{$task->name}}</span>
                    @endforeach
                    @endif
                </x-slot>
            </x-ez-card>
        </div>
    </div>

</div>
