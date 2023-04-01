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
                    <br>
                    Fax: +385 (0) 52 451 206
                    <br>

                    E-mail: reservations@valamar.com
                </p>

            </div>

        </x-mail.logo>
        <div class="" style="padding-top: 20px;"></div>

        <x-mail.row>
            <div class="" style="padding: 3px 0 0 0; text-align: right ;width: 100%; margin-bottom: 25px">
                <p style="width: 100%">
                    {{__('mail.cancelled_at')}}: {{$reservation->updated_at->format('d.m.Y H:i')}}
                    <br>
                    {{__('mail.reservation_holder')}}: {{$reservation->lead_traveller->full_name}}
                    <br>
                    <b>{{__('mail.reservation_number')}}:</b> #{{$reservation->id}}
                    <br>

                </p>

            </div>
        </x-mail.row>

        <x-mail.row>


            <table style="  border-collapse: unset ;border: 1px solid #363636; width: 100%;font-size: 11px">
                <thead>
                <tr style="border: 1px solid black;font-weight: 700;">
                    <td style="border: 1px solid black;padding:5px 5px">{{__('mail.no')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;">{{__('mail.code')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;">{{__('mail.service_name')}}</td>

                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">{{__('mail.without_vat')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">{{__('mail.vat')}}%</td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">{{__('mail.vat_amount')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">{{__('mail.total')}}</td>
                </tr>
                </thead>

                <tbody>
                @foreach($reservation->price_breakdown as $pbItem)
                    @if(\Arr::get($pbItem,'item') =='transfer_price')
                        <tr style="border: 1px solid black;">
                            <td style="padding:5px 5px;border: 1px solid black;">{{$loop->index + 1}}</td>

                            <td style="padding:5px 5px;border: 1px solid black;">
                                {{Arr::get($pbItem,'price_data.opera_package_id')}}
                            </td>
                            <td style="padding:5px 5px;border: 1px solid black;">{{\App\Actions\Breakdown\GetPriceBreakdownItemLabel::run($pbItem)}} </td>
                            <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                                <b>-{{$reservation->getPriceWithoutVat()}}</b></td>

                            <td style="padding:5px 5px;border: 1px solid black;text-align: right"><b>
                                    @if($reservation->included_in_accommodation_reservation)
                                        0
                                    @else
                                        25%

                                    @endif</b></td>
                            <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                                <b>-{{$reservation->getVatAmount()}}</b></td>
                            <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                                <b>-{{$reservation->getPrice()}}</b></td>
                        </tr>
                    @endif

                @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" style="border: 1px solid black; text-align: right;padding:5px 5px">
                        <b>{{__('mail.total_price')}}: </b>
                    </td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                        <b>-{{$reservation->getPriceWithoutVat()}}</b></td>

                    <td style="padding:5px 5px;border: 1px solid black;text-align: right"><b></b></td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                        <b>-{{$reservation->getVatAmount()}}</b></td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                        <b>-{{$reservation->getPrice()}} / -{{$reservation->getPriceHRK()}}</b></td>

                </tr>
                </tfoot>

            </table>


            <p style="margin-bottom: 25px;margin-top: 25px;">{{__("mail.tax_recapitulation")}}</p>

            <table style="  border-collapse: unset ;border: 1px solid #363636; width: 100%;font-size: 11px">
                <thead>
                <tr style="border: 1px solid black;font-weight: 700;">
                    <td style="border: 1px solid black;padding:5px 5px">{{__('mail.tax_group')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;">{{__('mail.base')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;">{{__('mail.vat')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;">{{__('mail.total')}}</td>

                </tr>
                </thead>
                <tbody>
                <td style="padding:5px 5px;border: 1px solid black;text-align: left"><b>
                        @if($reservation->included_in_accommodation_reservation)
                            PPO
                        @else
                            {{__("mail.vat")}} 25%

                        @endif
                    </b></td>

                <td style="padding:5px 5px;border: 1px solid black;text-align: left"><b>
                        @if(!$reservation->included_in_accommodation_reservation)

                            -{{$reservation->getPriceWithoutVat()}}
                        @endif
                    </b></td>

                <td style="padding:5px 5px;border: 1px solid black;text-align: left"><b>
                        @if(!$reservation->included_in_accommodation_reservation)

                            -{{$reservation->getVatAmount()}}
                        @endif

                    </b></td>
                <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                    <b>-{{$reservation->getPrice()}}</b>
                </td>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" style="border: 1px solid black; text-align: right;padding:5px 5px">
                        <b>{{__('mail.total')}}: </b>
                    </td>
                    <td style="border: 1px solid black;text-align: right;padding:5px 5px">
                        <b>-{{$reservation->getPrice()}} / -{{$reservation->getPriceHRK()}}</b>
                    </td>
                </tr>
                </tfoot>
            </table>


        </x-mail.row>


        <div class="" style="padding-top: 40px;"></div>



        <x-mail.row>
            <br>
            <br>
        </x-mail.row>
        <div class="" style="padding-top: 40px;"></div>

        <x-mail.footer>
            {{__('mail.valamar_transfer_service')}}
        </x-mail.footer>
    </x-mail.body>


    <x-mail.footer-below>
        {{__('mail.not_fiscalized')}}
        <div class="" style="padding-top: 40px;"></div>
        <div style="position: fixed;max-width: 600px;padding-bottom: 25px; bottom: 0;">

            <p style="text-align: justify; font-size: 8px">
                @if($reservation->destination->owner_id == 1)
                    {{__('mail.guest.footer.valamar')}}
                @endif

                @if($reservation->destination->owner_id == 2)
                    {{__('mail.guest.footer.imperial')}}
                @endif

                @if($reservation->destination->owner_id == 3){
                {{__('mail.guest.footer.helios_faros')}}
                @endif
            </p>
        </div>
    </x-mail.footer-below>

</x-mail.layouts.main>
