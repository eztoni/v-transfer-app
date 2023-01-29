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
        <div class="" style="padding-top: 40px;"></div>


        <x-mail.row>




            <table style="  border-collapse: unset ;border: 1px solid #363636; width: 100%">
                <thead>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">{{__('mail.no')}}</td>
                    <td style="border: 1px solid black;">{{__('mail.code')}}</td>
                    <td style="border: 1px solid black;">{{__('mail.service_name')}}</td>
                    <td style="border: 1px solid black;text-align: right">{{__('mail.total')}}</td>
                </tr>
                </thead>

                <tbody>
                @foreach($reservation->price_breakdown as $pbItem)
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;">{{$loop->index}}</td>
                        <td style="border: 1px solid black;">EXTRA-{{Arr::get($pbItem,'opera_package_id')}}</td>
                        <td style="border: 1px solid black;">{{\App\Actions\Breakdown\GetPriceBreakdownItemLabel::run($pbItem)}} </td>
                        <td style="border: 1px solid black;text-align: right"><b>{{Arr::get($pbItem,'amount.formatted')}}</b></td>
                    </tr>
                @endforeach


                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" style="border: 1px solid black; text-align: right">
                        <b>{{__('mail.total_price')}}: </b>
                    </td>
                    <td style="border: 1px solid black;">
                        <b>{{\Cknow\Money\Money::EUR($reservation->price)}}</b>
                    </td>
                </tr>
                </tfoot>

            </table>

        </x-mail.row>


        <div class="" style="padding-top: 40px;"></div>


        <x-mail.row>
            <p><b>{{__('mail.terms_and_conditions')}}</b></p>
            <br>
            <p>{{__('mail.booking_confirmation.terms_and_conditions')}}</p>
        </x-mail.row>
        <div class="" style="padding-top: 40px;"></div>


    </x-mail.body>


    <x-mail.footer>
        {{__('mail.valamar_transfer_service')}}
    </x-mail.footer>

    <x-mail.footer-below>
        {{__('mail.not_fiscalized')}}
        <div class="" style="padding-top: 40px;"></div>
        <p style="text-align: justify; font-size: 8px">
            {{__('mail.guest.footer.valamar')}}
        </p>
    </x-mail.footer-below>

</x-mail.layouts.main>
