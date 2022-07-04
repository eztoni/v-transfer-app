<x-app-layout>

<div class="card bg-base-100 shadow-lg">

    <div class="card-body ">
        <h2 class="card-title">
            EZ Log View
        </h2>
        <table class="ds-table  w-full">
            <thead>
            <tr>
                <th></th>
                <th>Date</th>
                <th>Type</th>
                <th>Env</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($logs as $log)
                <tr>
                    <th>{{ $loop->index }}</th>
                    <td>{{ $log['date'] }}</td>
                    <td>{{ $log['type'] }}</td>
                    <td>{{ $log['env'] }}</td>
                    @if($log['type'] == 'DEBUG')
                        <td><div x-data="buildModal()">
                                <x-ez-modal id="{{ $loop->index }}" >

                                    <x-slot name="button" @click="fetchModalContent({{ $loop->index }})">
                                        View Message
                                    </x-slot>

                                    <p>{{ $log['message'] }}</p>
                                    <x-slot name="footer">
                                        <label for="{{ $loop->index }}" class="btn">Close</label>
                                    </x-slot>
                                </x-ez-modal>
                            </div></td>
                    @else
                        <td>{{ $log['message'] }}</td>
                    @endif

                </tr>

            @empty
                <tr>
                    <td colspan="999">
                        <div class="alert alert-warning">
                            <div class="flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     class="w-6 h-6 mx-2 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <label>Neposoji log file</label>
                            </div>
                        </div>
                    </TD>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>
</div>
    <script>
        //alpine js code
        function LogGet() {
            return {
                // other default properties
                dataa: null,
                fetchLog() {
                    this.isLoading = true;
                    fetch(`{{ route('logs') }}`)
                        .then(res => res.json())
                        .then(data => {
                            this.dataa = data['logs'];
                        });
                }
            }
        }

        function buildModal(){
            return{
                fetchModalContent(){
                   console.log(arguments[0])
                }
            }
        }
    </script>
</x-app-layout>

