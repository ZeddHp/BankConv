<?php

namespace CurrencyConverter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


require_once "vendor/autoload.php";

/**
 * Converts currency from EUR to selected currency
 *
 * @param int $balance
 * @param string $selectedCurrency
 * @return void
 * @throws GuzzleException
 */
function convertCurrency(int $balance, string $selectedCurrency): void
{
    $url = "https://www.latvijasbanka.lv/vk/ecb.xml";

    $client = new Client([
        "verify" => false
    ]);

    try {
        $response = $client->get($url);
    } catch (GuzzleException $e) {
        throw GuzzleException("Error while fetching currency data: " . $e->getMessage());
    }

    $body = $response->getBody()->getContents();

    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
    $json = json_encode($xml);
    $currencyData = json_decode($json, true);

    $currencyList = $currencyData["Currencies"]["Currency"];

    $result = 0;

    foreach ($currencyList as $currency) {
        if ($currency["ID"] === $selectedCurrency) {
            $id = $currency["ID"];
            $rate = $currency["Rate"];
            $result = $balance * (float)$rate;
            echo "You converted $balance EUR to $result $id";
            break;
        }
    }
}

// Example usage
convertCurrency(100, "USD");
