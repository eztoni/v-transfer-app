<?php

namespace App\Services\Swap;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Contract\ExchangeRateQuery;

class HnbService extends \Exchanger\Service\HttpService
{

    /**
     * @inheritDoc
     */
    public function getExchangeRate(ExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $rates = $this->request('https://api.hnb.hr/tecajn/v2?valuta=EUR&valuta=USD');
        $rates = json_decode($rates, true);
        $rateArray = [];
        foreach ($rates as $rate) {
            $rateArray[$rate['valuta']] = (float)\Str::replace(',', '.', $rate['srednji_tecaj']);
        }
        $rate = null;

        if ($exchangeQuery->getCurrencyPair()->getBaseCurrency() === 'HRK') {
            $rate = 1 / $rateArray[$exchangeQuery->getCurrencyPair()->getQuoteCurrency()];
        } elseif ($exchangeQuery->getCurrencyPair()->getQuoteCurrency() === 'HRK') {
            $rate = $rateArray[$exchangeQuery->getCurrencyPair()->getBaseCurrency()];
        }
        return $this->createInstantRate($exchangeQuery->getCurrencyPair(),$rate );
    }

    /**
     * @inheritDoc
     */
    public function supportQuery(ExchangeRateQuery $exchangeQuery): bool
    {
        if(count(array_intersect([$exchangeQuery->getCurrencyPair()->getBaseCurrency(),$exchangeQuery->getCurrencyPair()->getQuoteCurrency()], ['EUR', 'USD', 'HRK']))===2){
            if($exchangeQuery->getCurrencyPair()->getBaseCurrency() === 'HRK' || $exchangeQuery->getCurrencyPair()->getQuoteCurrency() ==='HRK'){
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'hnb';
    }
}
