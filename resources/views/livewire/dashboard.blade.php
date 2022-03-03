<div >
    <x-ez-card class="mb-4">
        <x-slot name="body">

          <?php





            $exchange = new Money\Exchange\SwapExchange(\Swap\Laravel\Facades\Swap::getFacadeRoot());
            $converter = new Money\Converter(new Money\Currencies\ISOCurrencies(), $exchange);
            $eur100 = Money\Money::USD(1000);
            $usd125 = $converter->convert($eur100, new Money\Currency('HRK'));

            dump( $eur100);
            dump( $usd125);

       ?>




        </x-slot>
    </x-ez-card>
</div>

