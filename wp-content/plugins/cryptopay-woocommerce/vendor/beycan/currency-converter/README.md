# Currency Converter

## About

It takes on the task of converting between fiat and cryptocurrencies.

## Installation

`composer require beycan/currency-converter`

## Usage

```php
use Beycan\CurrencyConverter;


$converter = new CurrencyConverter('cryptocompare | coinmarketcap', 'api key for coinmarketcap');
$paymentPrice = $converter->convert('USD', 'BTC', 15 /* USD Price */);

$paymentPrice // BTC Price
```