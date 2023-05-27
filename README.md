# intlCountries


 A PHP 7.0+ internationalization library and services for getting all the 
 - world countries, 
 - country states, 
 - country's currency/currencies, 
 - symbols, locale and 
 - languages. 
 
 It also includes: 
 - Number Formatter, 
 - Decimal Formatter and 
 - Currency Formatter : currency formatter by country name, isoAlphaCode2 or isoAlphaCode3.

 I am not going to bore you with lots of write-up to justify why you should or should not use this library as I strongly believe nobody says no to problem solving tools. So, here are the example as it can be used to solve internationalization problems:

 ```php

use Countries\Countries;

//VERY IMPORTANT - All function that returns an object also has there corresponding function that returns an array data set.

/**
 * GET ALL
 */
//Get all - COUNTRIES
$countries = Countries::getCountryRepository()->findAllCountry();

//Get all - CURRENCIES - countries and there currency(s) information
$currencies = Countries::getCurrencyRepository()->findAllCountryAndCurrency();

//Get all - LANGUAGE - locales of all countries. Data incliudes: isoAlphaCode2, isoAlphaCode3, locales, defaultLocale, language of countries.
$locales = Countries::getLocaleRepository()->findAllLocales();


/**
 * FIND SOMETHING SPECIFIC
 */ 
//Get a particular country locales information - By countryName OR country isoAlphaCode2 OR country isoAlphaCode2. 
$countryNameORisoAlphaCode2ORisoAlphaCode2 = 'US'; //OR USD OR United States of America
$countryLocales = Countries::getLocaleRepository()->findLocale(string $countryNameORisoAlphaCode2ORisoAlphaCode2)

//Get a particular country information - By countryName OR country isoAlphaCode2 OR country isoAlphaCode2. 
//Data includes: name, all states in that country, currencies, default_country of that country, etc...
$countryNameORisoAlphaCode2ORisoAlphaCode2 = 'US'; //OR USD OR United States of America
$countryInformation = (Countries::getCountryRepository()->findCountry($countryNameORisoAlphaCode2ORisoAlphaCode2));

//Get a particular country currency detailed information. For example:
$code = 'NGN';
$currency = (Countries::getCurrencyRepository()->findCurrencyDetails($code));

/**
 * FORMATTERS
 */ 
//Format Currency using countryName OR country isoAlphaCode2 OR country isoAlphaCode2
echo $formatedAmount = Countries::formatCurrencyByCountry(5.0, 'NG');
echo $formatedAmount = Countries::formatCurrencyByCountry(5.0, 'NGA');
echo $formatedAmount = Countries::formatCurrencyByCountry(5.0, 'Nigeria');

//Similarly format Decimal Number By countryName OR country isoAlphaCode2 OR country isoAlphaCode2
echo $formatedAmount = Countries::formatDecimalByCountry(5.0, 'NG');
echo $formatedAmount = Countries::formatDecimalByCountry(5.0, 'NGA');
echo $formatedAmount = Countries::formatDecimalByCountry(5.0, 'Nigeria');

//VERY IMPORTANT - All function that returns an object also has there corresponding function that returns an array data set.

 ```

 If you check the `Countries` class, there are lots functions you can use for so many different internationalization. The example above are a few of how useful this library is to you. :)

