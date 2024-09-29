@section('max_w','780px')
<?php
$locale = app()->getLocale();

$res_type = $reservation->isRoundTrip() ? '2' : '1';

$locale_configuration = array(
    'hr' => array(
        1 => array(
            'footer-font-size' => 7,
            'footer-upper-padding' => 30,
            'footer-above-padding' => 20
        ),
        2 => array(
            'footer-font-size' => 6,
            'footer-upper-padding' => 1,
            'footer-above-padding' => 10
        )
    ),
    'en' => array(
        1 => array(
            'footer-font-size' => 7,
            'footer-upper-padding' => 30,
            'footer-above-padding' => 20
        ),
        2 => array(
            'footer-font-size' => 6,
            'footer-upper-padding' => 0,
            'footer-above-padding' => 12
        )
    ),
    'de' => array(
        1 => array(
            'footer-font-size' => 6,
            'footer-upper-padding' => 10,
            'footer-above-padding' => 20
        ),
        2 => array(
            'footer-font-size' => 5,
            'footer-upper-padding' => 10,
            'footer-above-padding' => 0
        )
    ),
    'it' => array(
        1 => array(
            'footer-font-size' => 6,
            'footer-upper-padding' => 10,
            'footer-above-padding' => 20
        ),
        2 => array(
            'footer-font-size' => 4.8,
            'footer-upper-padding' => 0,
            'footer-above-padding' => 5
        )
    )
);


if($reservation->destination->owner_id > 1){

    $locale_configuration = array(
        'hr' => array(
            1 => array(
                'footer-font-size' => 6,
                'footer-upper-padding' => 15,
                'footer-above-padding' => 0
            ),
            2 => array(
                'footer-font-size' => 3.2,
                'footer-upper-padding' => 0,
                'footer-above-padding' => 10
            )
        ),
        'en' => array(
            1 => array(
                'footer-font-size' => 6,
                'footer-upper-padding' => 15,
                'footer-above-padding' => 0
            ),
            2 => array(
                'footer-font-size' => 3.2,
                'footer-upper-padding' => 0,
                'footer-above-padding' => 10
            )
        ),
        'de' => array(
            1 => array(
                'footer-font-size' => 5,
                'footer-upper-padding' => 15,
                'footer-above-padding' => 0
            ),
            2 => array(
                'footer-font-size' => 3.2,
                'footer-upper-padding' => 0,
                'footer-above-padding' => 5
            )
        ),
        'it' => array(
            1 => array(
                'footer-font-size' => 5,
                'footer-upper-padding' => 15,
                'footer-above-padding' => 0
            ),
            2 => array(
                'footer-font-size' => 3.2,
                'footer-upper-padding' => 0,
                'footer-above-padding' => 10
            )
        )
    );
}

?>
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
            <div class="" style="padding: 3px 25px 0 0; text-align: right ;width: 100%;font-size:10px">
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
                <p style="width: 100%;font-size: 12px !important">
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
            @if($reservation->included_in_accommodation_reservation == 0 && $reservation->v_level_reservation)
                <p align="right">{{__('mail.invoice_no')}}: {{$reservation->getInvoiceData('invoice_number')}}</p><br/>
            @endif

            <table style="  border-collapse: unset ;border: 1px solid #363636; width: 100%;font-size: 11px;table-layout: fixed" >
                <thead>
                <tr style="border: 1px solid black;font-weight: 700;">
                    <td style="border: 1px solid black;padding:5px 5px" width="13%">{{__('mail.no')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;" width="11%">{{__('mail.code')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;" width="36%">{{__('mail.service_name')}}</td>
                    <td style="padding:5px 5px;border: 1px solid black;text-align: right" width="10%">{{__('mail.total')}}</td>
                </tr>
                </thead>

                <tbody>
                @foreach($reservation->getCancellationItemBreakDown('items') as $pbItem)

                        <tr style="border: 1px solid black;">
                            <td style="padding:5px 5px;border: 1px solid black;">{{$loop->index + 1}}.</td>

                            <td style="padding:5px 5px;border: 1px solid black;">
                                {{$pbItem['code']}}
                            </td>
                            <td style="padding:5px 5px;border: 1px solid black;">{{$pbItem['transfer']}} </td>
                            <td style="padding:5px 5px;border: 1px solid black;text-align: right">
                                <b>{{$pbItem['price']}}</b> </td>
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
                            <b>{{$reservation->getCancellationItemBreakDown('items_total')}}</b>
                        @else
                            <b>0,00</b>
                        @endif

                    </td>
                </tr>
                </tfoot>

            </table>

                @if(\Arr::get($reservation->transfer_price_state,'price_data.tax_level') == 'PPOM')
                    <div align="left">
                        <p style="font-style: italic;font-size: 11px !important">* {{__('mail.special_taxing')}} </p>
                    </div>
                @endif
        </x-mail.row>

        <div class="" style="padding-top: 20px;"></div>
        <x-mail.row>

            <div style="font-size: 11px !important;">
                <table align="center">
                    <tr>
                        <td align="right"><b>{{__('mail.issue_location')}}:</b></td>
                        <td style="padding: 0 10px;">{{$reservation->getAccommodationData('name')}}</td>
                    </tr>
                    <tr>
                        <td align="right"><b>{{__('mail.operator')}}:</b></td>
                        <td style="padding: 0 10px;">{{$reservation->getOperatorName()}}</td>
                    </tr>
                    @if($reservation->getInvoiceData('zki','reservation'))
                        <tr>
                            <td align="right"><b>ZKI:</b></td>
                            <td style="padding: 0 10px;">{{$reservation->getInvoiceData('zki','reservation')}}</td>
                        </tr>
                    @endif
                    <!-- Jir -->
                    @if($reservation->getInvoiceData('jir'))
                        <tr>
                            <td align="right"><b>JIR:</b></td>
                            <td style="padding: 0 10px;">{{$reservation->getInvoiceData('jir','reservation')}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td align="right"><b>{{__('mail.voucher_number')}}:</b></td>
                        <td style="padding: 0 10px;">{{$reservation->id}}</td>
                    </tr>
                    <tr>
                        <td align="right"><b>{{__('mail.issue_date_and_time')}}:</b></td>
                        <td style="padding: 0 10px;">{{\Carbon\Carbon::parse($reservation->created_at->format('d.m.Y H:i'))->format('d.m.Y H:i')}}</td>
                    </tr>
                    <tr>
                        <td align="right"><b>{{__('mail.accommodation')}}:</b></td>
                        <td style="padding: 0 10px;">{{$reservation->getAccommodationData('name')}}</td>
                    </tr>
                </table>
            </div>

            <div class="" style="padding-top: 15px;"></div>
            <p style="font-size: 12px !important"><b>{{__('mail.terms_and_conditions')}}</b></p>
            <br>
            <p style="font-size: 11px !important">{{__('mail.booking_confirmation.terms_and_conditions')}}</p>
            <div class="" style="padding-bottom: {{ $locale_configuration[$locale][$res_type]['footer-above-padding'] }}px;"></div>
        </x-mail.row>

        <x-mail.footer>
            {{__('mail.valamar_transfer_service')}}<br>
            {{html_entity_decode(__('mail.not_fiscalized'))}}
            <div class="" style="padding-top: {{ $locale_configuration[$locale][$res_type]['footer-upper-padding'] }}px;"></div>
            <x-mail.footer-below>


                <div style="position: relative;">

                    <p style="text-align: justify; font-size: {{ $locale_configuration[$locale][$res_type]['footer-font-size'] }}px">

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
            <div class="" style="padding-top: 10px;"></div>
        </x-mail.footer>
        <x-mail.logo>
            <div class="" style="padding: 3px 25px 0 0; text-align: right ;width: 100%;font-size:10px">
                <p style="width: 100%">
                    {!! \App\Actions\Mail\GetMailHeaderAddressAndName::run($reservation) !!}
                    <br/>
                    E-mail: reservations@valamar.com
                </p>

            </div>
            <div class="" style="padding-top: 20px;"></div>
        </x-mail.logo>

        <x-mail.row>
            <div class="" style="padding-top: 20px;"></div>
            <p style="font-size: 11px !important"><b>{{__('mail.gdpr_title')}}:</b></p>
            <br>
            <ul style="font-size: 11px !important;  list-style-type: circle;" >
                <li style="margin-left:2%">{{__('mail.gdpr_1')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_2')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_3')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_4')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_5')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_6')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_7')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_8')}}</li>
                <li style="margin-left:2%">{{__('mail.gdpr_9')}}</li>

            </ul>
            <div class="" style="padding-bottom: 20px;"></div>
        </x-mail.row>
        <x-mail.footer_bottom>
            {{__('mail.valamar_transfer_service')}}<br>
            {{html_entity_decode(__('mail.not_fiscalized'))}}
            <div class="" style="padding-top: 20px;"></div>
            <x-mail.footer-below>


                <div style="position: relative;">

                    <p style="text-align: justify; font-size: {{ $locale_configuration[$locale][$res_type]['footer-font-size'] }}px">

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
        </x-mail.footer_bottom>

    </x-mail.body>


</x-mail.layouts.main>
