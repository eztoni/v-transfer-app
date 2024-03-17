@section('max_w','780px')

<x-mail.layouts.main>

    <x-slot name="head_after">
        <style>
            @font-face {
                font-family: 'font';
                font-style: normal;
                font-weight: normal;

            }

            p {
                font-weight: normal !important;
            }
        </style>

    </x-slot>

    <x-mail.body>

        <x-mail.logo>
            <div class="" style="padding: 3px 25px 0 0; text-align: right ;width: 100%">
                <p style="width: 100%">
                    {!! \App\Actions\Mail\GetMailHeaderAddressAndName::run($reservation) !!}
                    <br/>
                    E-mail: reservations@valamar.com
                </p>

            </div>

        </x-mail.logo>
        <div class="" style="padding-top: 20px;"></div>

        <x-mail.row>
            <div class="" style="padding: 3px 0 0 0; text-align: right ;width: 100%; margin-bottom: 25px">
                <p style="width: 100%;font-size: 13px !important">
                    <b>{{__('mail.transfer_reservation_confirmation')}}:</b> {{gmdate('Y').'-'.$reservation->getInvoiceData('invoice_number','reservation')}}
                    <br/>
                    <b>{{__('mail.accommodation_reservation_holder')}}:</b> {{$reservation->lead_traveller->full_name}}
                    <br>
                    <b>{{__('mail.accommodation_reservation_number')}}:</b> {{$reservation->getAccommodationReservationCode()}}
                    <br>

                </p>

            </div>
        </x-mail.row>

        <x-mail.row>
            <table style="  border-collapse: unset ;border: 1px solid #363636; width: 100%;font-size: 11px;table-layout: fixed">
                <thead>
                <tr style="border: 1px solid black;font-weight: 700;">
                    <td style="border: 1px solid black;padding:5px 5px;" width="8%"  >{{__('mail.no')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;" width="8%">{{__('mail.code')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;" width="42%">{{__('mail.service_name')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right" width="40%">{{__('mail.total')}}</td>
                </tr>
                </thead>

                <tbody>
                @foreach($reservation->getConfirmationItemBreakdown('items') as $pbItem)

                        <tr style="border: 1px solid black;">
                            <td style="padding:5px 5px;border: 1px solid black;">{{$loop->index + 1}}.</td>

                            <td style="padding:5px 5px;border: 1px solid black;">
                                {{Arr::get($pbItem,'code')}}
                            </td>
                            <td style="padding:5px 5px;border: 1px solid black;">{{Arr::get($pbItem,'transfer')}}</td>
                            <td style="padding:5px 5px;border: 1px solid black;text-align: right">

                            @if($reservation->included_in_accommodation_reservation == 0 && $reservation->v_level_reservation == 0)
                                <b>{{Arr::get($pbItem,'price')}}</b></td>
                            @else
                                <b>0,00 €</b>
                            @endif
                        </tr>

                @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" style="border: 1px solid black; text-align: right;padding:5px 5px">
                        <b>{{__('mail.total_eur')}}: </b>
                    </td>

                    <td style="border: 1px solid black;text-align: right;padding:5px 5px">
                        @if($reservation->included_in_accommodation_reservation == 0 && $reservation->v_level_reservation == 0)
                            <b>{{$reservation->getConfirmationItemBreakdown('items_total')}}</b>
                        @else
                            <b>0,00</b>
                        @endif

                    </td>
                </tr>
{{--                <tr>--}}
{{--                    <td colspan="3" style="border: 1px solid black; text-align: right;padding:5px 5px">--}}
{{--                        <b>{{__('mail.total_hrk')}}: </b>--}}
{{--                    </td>--}}
{{--                    <td style="border: 1px solid black;text-align: right;padding:5px 5px">--}}
{{--                        @if($reservation->included_in_accommodation_reservation == 0 && $reservation->v_level_reservation == 0)--}}
{{--                            <b>{{$reservation->getConfirmationItemBreakdown('items_total_hrk')}}</b>--}}
{{--                        @else--}}
{{--                            <b>0,00</b>--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                </tr>--}}


                </tfoot>

            </table>

{{--            <p style="float: right;font-style: italic;font-size:10px;padding:2px">{{__('mail.price_info')}}</p>--}}


        </x-mail.row>

        <div class="" style="padding-top: 40px;"></div>

        <x-mail.row>

            @if(\Arr::get($reservation->transfer_price_state,'price_data.tax_level') == 'PPOM')
                <br/><p style="float: right;font-style: italic;font-size: 13px !important"> * Posebni postupak oporezivanja putničkih agencija sukladno čl. 91. Zakona o PDV-u</p>
            @endif
            <div align="center" style="font-size: 13px !important">
                <!-- Issue Location -->
                <br/>
                <p><b>Issue Location:</b> {{$reservation->getAccommodationData('name')}}</p>
                <!-- Issue Date and Time -->
                <p><b>Issue Date and Time:</b>  {{\Carbon\Carbon::parse($reservation->created_at->format('d.m.Y H:i'))->format('d.m.Y H:i')}}</p>
                <!-- Operator -->
                <p><b>Operator:</b> {{$reservation->getOperatorName()}}</p>

                <!-- ZKI -->
                @if($reservation->getInvoiceData('zki','reservation'))
                    <p><b>ZKI:</b> {{$reservation->getInvoiceData('zki','reservation')}}</p>
                @endif

                <!-- Jir -->
                @if($reservation->getInvoiceData('jir'))
                    <p><b>JIR:</b> {{$reservation->getInvoiceData('jir','reservation')}}</p>
                @endif
                <p><b>Accommodation:</b> {{$reservation->getAccommodationData('name')}}</p>

            </div>


            <div class="" style="padding-top: 20px;"></div>
            <p style="font-size: 13px !important"><b>{{__('mail.terms_and_conditions')}}</b></p>
            <br>
            <p style="font-size: 13px !important">{{__('mail.booking_confirmation.terms_and_conditions')}}</p>
            <div class="" style="padding-bottom: 20px;"></div>
        </x-mail.row>

        <x-mail.footer style="position: fixed;bottom:0 !important;">
            {{__('mail.valamar_transfer_service')}}<br>
            {{html_entity_decode(__('mail.not_fiscalized'))}}
            <div class="" style="padding-top: 20px;"></div>
            <x-mail.footer-below>


                <div style="position: relative; bottom: 0">

                    <p style="text-align: justify; font-size: 8px">

                        <!-- Valamar Riviera Footer -->
                        @if($reservation->destination->owner_id == 1)
                            {{__('mail.guest.footer.valamar')}}
                        @endif

                        <!-- Imperial Rab Footer -->
                        @if($reservation->destination->owner_id == 2)
                            {{__('mail.guest.footer.imperial')}}
                        @endif

                        <!-- Helious Faros -->
                        @if($reservation->destination->owner_id == 3){
                        {{__('mail.guest.footer.helios_faros')}}
                        @endif
                    </p>
                </div>
            </x-mail.footer-below>
        </x-mail.footer>


    </x-mail.body>




</x-mail.layouts.main>
