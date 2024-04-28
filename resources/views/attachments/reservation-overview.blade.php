@section('max_w','auto')


<x-mail.layouts.main>

@php
    /**
* @var \Carbon\Carbon $dateFrom
 */
@endphp
    <x-mail.body>
        <x-mail.logo>
            <div class="" style="padding: 3px 25px 0 0; text-align: right ;width: 100%">
                <p style="width: 100%">
                    Rezervacije za <b>{{$hotel}}</b> za period:
                    <br>
                  <b> {{$from->format('d.m.Y')}} - {{$to->format('d.m.Y')}} </b>

                </p>

            </div>

        </x-mail.logo>

        <x-mail.row>
            <div class="" style="padding-top: 20px;"></div>

            <table style="border-collapse: unset;border: 1px solid #363636; width: 100%;font-size: 11px;line-height: 11px">
                <thead>
                <tr style="border: 1px solid black;font-weight: 700;">
                    <td style="border: 1px solid black;padding:5px 5px">#</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Gost</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Datum</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Partner</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Pax</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Ruta</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Cijena</td>
                    <td style="padding:5px 5px;border: 1px solid black;">Opera</td>
                </tr>
                </thead>

                <tbody>

                @foreach($reservations as $reservation)
                    @php
                        $rObject = Arr::get($reservation,'reservation');

                    @endphp
                    <tr>
                        <td style="border: 1px solid black;" align="center">{{Arr::get($reservation,'id')}}</td>
                        <td style="border: 1px solid black;" align="center">{{Arr::get($reservation,'name')}}</td>
                        <td style="border: 1px solid black;" align="center">
                            <p class="flex gap-2">

                                <label> {{Arr::get($reservation,'formatted_date_time')}}</label>
                            </p>

                        </td>
                        <td style="border: 1px solid black;" align="center">{{Arr::get($reservation,'partner')}}</td>
                        <td style="border: 1px solid black;" align="center">
                            <span>Odrasli: {{Arr::get($reservation,'adults')}}</span>

                            @if(Arr::get($reservation,'children'))
                                <br>
                                <span>Djeca: {{Arr::get($reservation,'children')}}</span>

                            @endif
                            @if(Arr::get($reservation,'infants'))
                                <br>
                                <span>Dojenčad: {{Arr::get($reservation,'infants')}}</span>
                            @endif

                        </td>
                        <td style="border: 1px solid black;" align="center">
                            <p class="flex gap-2">
                                <label>  {{Arr::get($reservation,'formatted_route')}}
                            </p>
                        </td>

                        <td style="border: 1px solid black;" align="center"><p class="flex gap-2">{{Arr::get($reservation,'price')}} €</p></td>
                        <td class="flex gap-2" style="border: 1px solid black;" align="center"><b>Resv ID</b><br/>{{Arr::get($reservation,'opera_resv_id')}}<br/><b>Conf. ID</b><br/>{{Arr::get($reservation,'opera_confirmation_id')}}</td>
                    </tr>
                @endforeach
                </tbody>


            </table>
            <div class="" style="padding-top: 20px;"></div>

        </x-mail.row>

    </x-mail.body>
</x-mail.layouts.main>
