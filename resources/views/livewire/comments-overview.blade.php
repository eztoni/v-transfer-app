<div>

    <div class="flex flex-row-reverse mb-4">
        <button class="ml-4 btn  btn-primary" wire:click="addNewComment"> Dodaj Komentar</button>

        <a href="{{ route('partneri') }}"><button class="btn  "> Pregled Partnera</button></a>
    </div>

    @if($showForm)
        <x-ez-card class="mb-4">
            <x-slot name="body">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Komentar:</span>
                    </label>
                    <textarea placeholder="Komentar" wire:model.debounce.300ms="editData.comment" class="textarea textarea-primary" rows="6"></textarea>
                    @error('editData.comment')
                    <x-input-alert type='warning'>Polje za komentar je obavezno.</x-input-alert>@enderror
                </div>


                <div class="flex flex-row-reverse my-4">
                    <button wire:click.prevent="saveComment()" class="btn btn-primary ml-4">Spremi</button>

                    <button wire:click.prevent="closeForm()" class="btn btn-warning">Zatvori</button>
                </div>

            </x-slot>
        </x-ez-card>
    @endif

    @forelse ($comments as $comment)
        <x-ez-card class="{{ $comment->task_id > 0 ? 'border-l-4' : ''  }} border-primary mb-4">
            <x-slot name="body" class="pb-1">
                <div class=" absolute top-0 left-0 w-full pl-2 pt-1 border-b  flex justify-between">

                @if($comment->task_id > 0 && !$partner->tasks->where('id','=',$comment->task_id)->isEmpty())

                        <span class="badge-ghost badge">Komentar za posao - {{($partner->tasks->where('id','=',$comment->task_id))->first()->name}}</span>
                    @else
                        <span class="badge-ghost badge ml-2">Komentar:</span>

                    @endif
                    <span class="pr-4  ">Datum: {{Carbon\Carbon::parse($comment->created_at)->format('d.m.Y')}}</span>
                </div>
                <p>{{$comment->comment}}</p>

            </x-slot>
        </x-ez-card>
    @empty
        <x-ez-card>
            <x-slot name="body">
                <h1>Nema dodanih komentara za odabranog partnera.</h1>
            </x-slot>
        </x-ez-card>
    @endforelse
    {{$comments->links()}}
    @php Debugbar::stopMeasure('Debug'); @endphp

</div>

