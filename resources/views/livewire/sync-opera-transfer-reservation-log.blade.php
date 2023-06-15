
@if(!$this->log)
    <p>No Log Present</p>
@else

    @php
        if(count($this->log) == 1 ){
           $key = array_keys($this->log)[0];

           if(!empty($this->log[$key])){

               $updated_at = $this->log[$key]->updated_at;
               $log_message = $this->log[$key]->log_message;
               $log_id = $key;
               $request = json_decode($this->log[$key]->opera_request,true);
               $response = json_decode($this->log[$key]->opera_response,true);

               echo '<div class=" my-4">
                    <p class="text">'.$updated_at.' - '.$log_message.' - LogID: #'.$key.'</p>
                    <div class="ds-divider"></div>
                    <p class="text">[REQUEST]</p>
                    <pre>'.json_encode($request,JSON_PRETTY_PRINT).'</pre>
                    <p class="text">[RESPONSE]</p>
                    <pre>'.json_encode($response,JSON_PRETTY_PRINT).'</pre>
                    <div class="flex justify-end">
                        <x-button label="Close"  negative wire:click="close"/>
                    </div>
                </div>';
           }

        }

    @endphp
                @if(count($this->log) > 1)
                    <div class="max-h-96 overflow-y-scroll">
                        <table class="ds-table ds-table-compact w-full  ">
                            <thead>
                            <tr>
                                <th>#Log ID</th>
                                <th>#Reservation ID</th>
                                <th>Log Message</th>
                                <th>Sync Status</th>
                                <th>Updated At</th>
                                <th>View Log</th>
                            </tr>
                            </thead>
                            <tbody
                    @foreach($this->log as $log)
                        <tr>
                            <th>{{$log->id}}</th>
                            <td>{{$log->reservation_id}}</td>
                            <td>{{$log->log_message}}</td>
                            <td>{{ucfirst($log->sync_status)}}</td>
                            <td>{{$log->updated_at}}</td>
                            <td><x-button sm icon="external-link" wire:click="openOperaSyncLogModal({{$log->id}})">View Sync Log</x-button></td>
                        </tr>
                    @endforeach
                    </tbody>
                        </table>
                    </div>
                @endif
@endif

