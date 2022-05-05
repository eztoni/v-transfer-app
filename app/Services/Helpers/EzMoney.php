<?php

namespace App\Services\Helpers;

use Cknow\Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Parser\IntlLocalizedDecimalParser;

class EzMoney
{
    private $moneyParser;

    const MONEY_REGEX = '/^-?\d{1,3}(?:\.\d{3})*(?:,\d+)?$/';

    public function __construct()
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::DECIMAL);
        $this->moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);

    }


    public function parseForDb(string $amount, string $currency = 'EUR'):string
    {

        return $this->moneyParser->parse($amount, new Currency('EUR'))->getAmount();
    }

    public function format(string $price):string
    {

        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::DECIMAL);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);

       return $moneyFormatter->format(Money::EUR($price)->getMoney());
    }

}
