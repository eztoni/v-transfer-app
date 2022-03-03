<div >
    <x-ez-card class="mb-4">
        <x-slot name="body">

          <?php




          $rate = Swap\Laravel\Facades\Swap::latest('EUR/HRK');

            $exchange = new Money\Exchange\SwapExchange(\Swap\Laravel\Facades\Swap::getFacadeRoot());
            $converter = new Money\Converter(new Money\Currencies\ISOCurrencies(), $exchange);
            $eur100 = Money\Money::EUR(100);
            $usd125 = $converter->convert($eur100, new Money\Currency('HRK'));
            $rate = \Swap\Laravel\Facades\Swap::latest('EUR/HRK');

            // 1.129
            ECHO $rate->getValue();
            dump( $eur100);
            dump( $usd125);


       ?>




        </x-slot>
    </x-ez-card>
</div>

