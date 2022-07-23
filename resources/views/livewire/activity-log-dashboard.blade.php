<div x-data="app()">

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Activity Log Dashboard
        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search"  class="input input-primary my-2" placeholder="Find Activity">
            <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Subject Type</th>
                    <th>Causer</th>
                    <th>Description</th>
                    <th>Timestamp</th>
                    <th class="text-center">View</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($activities as $activity)


                    <tr>
                        <th>{{ $activity->id }}</th>
                        <td>{{ \Illuminate\Support\Str::limit($activity->subject_type, 50, $end='...') }}</td>
                        <th>{{ $activity->causer->name }}</th>
                        <td>{{ \Illuminate\Support\Str::limit($activity->description, 50, $end='...') }}</td>
                        <td>{{$activity->created_at}}</td>
                        <td class="text-center">
                            <button wire:click="openActivityModal({{$activity->id}})" class="btn btn-sm btn-success">
                                View
                            </button>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="999">
                            <div class="ds-alert ds-alert-warning">
                                <div class="flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         class="w-6 h-6 mx-2 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <label>No activities</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>


            @if($activity)
                <div class="modal {{ $activityModal ? 'modal-open fadeIn' : '' }}">
                    <div class="modal-box max-h-screen overflow-y-auto">
                        Activity #ID {{ $activity->id }}
                        <hr class="my-4">

                        <p><b>Causer</b> : {{$activity->causer->name}}</p>
                        <p><b>Causer type</b> : {{$activity->causer_type}}</p>
                        <p><b>Created at</b> : {{$activity->created_at}}</p>


                        <hr class="my-4">

                        @if($activity->changes)

                            <div class="collapse w-full rounded-box collapse-arrow ">
                                <input type="checkbox">
                                <div class="collapse-title text-md font-medium">
                                    Changes
                                </div>
                                <div class="max-h-1 collapse-content">
                                    <div class="overflow-x-auto overflow-y-auto" style="max-height:250px !important;">
                                        <pre x-ref="jsonText">{{json_encode($activity->changes, JSON_PRETTY_PRINT)}}</pre>
                                    </div>
                                </div>
                            </div>

                        @endif

                        <hr class="my-4">

                        <div class="collapse w-full rounded-box collapse-arrow ">
                            <input type="checkbox">
                            <div class="collapse-title text-md font-medium">
                                JSON Raw Data
                            </div>
                            <div class="max-h-1 collapse-content">
                                <div class="overflow-x-auto overflow-y-auto" style="max-height:250px !important;">
                                    <button  @click="copyTextToClipBoard" class="btn btn-xs"><i class="fas fa-clipboard-list"></i> <span class="pl-2">Copy</span></button> <span class="text-bg-primary" x-show="open">Data copied!</span>
                                    <pre x-ref="jsonText">{{json_encode($activity, JSON_PRETTY_PRINT)}}</pre>
                                </div>
                            </div>
                        </div>




                        <div class="mt-4 flex justify-between">
                            <button wire:click="closeActivityModal()" class="btn btn-sm ">Close</button>
                        </div>
                    </div>
                </div>
            @endif


        </x-slot>

    </x-ez-card>
</div>
<script>
    function app(){
        return {
            open: false,
            copyTextToClipBoard(){
                this.open = true
                setTimeout(() => this.open = false, 2000)
                var json_text = this.$refs.jsonText.innerText;

                //Copy to clipboard
                var input = document.createElement('textarea');
                input.innerHTML = json_text;
                document.body.appendChild(input);
                input.select();
                var result = document.execCommand('copy');
                document.body.removeChild(input);
                return result;
            },
        }
    }
</script>

