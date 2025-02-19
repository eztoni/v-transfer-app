<div class=" my-4">
    @if($modifications = $this->reservation->getRawModifications())
        @foreach($modifications as $res_id => $mod_data)
            @foreach($mod_data as $timestamp => $mods)
                <b><h4>Direction Reservation ID: #{{$res_id}} Modified at: {{$timestamp}} by {{$mods['updated_by_user']}}</h4></b>
                <p>The following parameter(s) where changed: {{implode(',',$mods['modifications'])}}</p>
                <br/><hr><br/>
            @endforeach
        @endforeach
    @endif

</div>
