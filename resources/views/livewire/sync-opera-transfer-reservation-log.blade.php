<div class=" my-4">
    <p class="text">{{$this->log['updated_at']}} - {{$this->log['log_message']}} - LogID: #{{$this->log['id']}}</p>
    <div class="ds-divider"></div>
    <p class="text">[REQUEST]</p>
    <pre>{{json_encode($this->log['opera_request'],JSON_PRETTY_PRINT)}}</pre>
    <p class="text">[RESPONSE]</p>
    <pre>{{json_encode($this->log['opera_response'],JSON_PRETTY_PRINT)}}</pre>
    <div class="flex justify-end">
        <x-button label="Close"  negative wire:click="close"/>
    </div>
</div>
