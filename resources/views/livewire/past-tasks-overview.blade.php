<div>
    @forelse ($pastTasks as $task)
        <x-ez-card class="mb-4">
            <x-slot name="body" class=" p-4">
                <h1 class="text-xl"><b>{{$task->name}}</b></h1>
                <hr class="opacity-75">

                <div class="mt-2 flex  flex-wrap xl:flex-nowrap">


                    <div class="flex-none w-64">
                        <div class=" p-0">
                            <p class="">Datum izvršenja posla:</p>
                            <p class="font-semibold  badge badge-ghost">{{Carbon\Carbon::parse($task->date_completed)->format('d.m.Y')}} </p>
                        </div>
                    </div>
                    <div class="flex-none w-64">
                        <div class=" p-0">
                            <p class="">Partner:</p>
                            <p class=" badge badge-ghost ">{{$task->partner->business_name}} </p>
                        </div>
                    </div>

                    @if(!$task->workers->isEmpty())
                    <div class=" flex-grow ">
                        <div class=" p-0">
                            <p class="">Radnici</p>
                            <p class="font-semibold ">
                                @foreach($task->workers as $worker)
                                   <span class="badge badge-ghost"> {{$worker->name . ' '.$worker->surname }}{{($loop->last)?'.':', '}}</span>
                                @endforeach
                            </p>
                        </div>
                    </div>
                    @endif
                    @if(!empty($task->comment))
                        <div class=" flex-grow  ">
                            <div class=" p-0">
                                <p class="">Komentar:</p>
                                <p class="  ">{{$task->comment->comment}}</p>
                            </div>
                        </div>
                    @endif






                </div>

            </x-slot>
        </x-ez-card>
    @empty
        <x-ez-card class="mb-4">
            <x-slot name="body">
                <h1>Nema odrađenih poslova.</h1>
            </x-slot>
        </x-ez-card>
    @endforelse
    <x-ez-card>
        <x-slot name="body">
            {{$pastTasks->links()}}
        </x-slot>
    </x-ez-card>
</div>
