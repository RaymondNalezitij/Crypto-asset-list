<?php

namespace App\Controllers;

use App\Models\CryptoAsset;
use App\View;

class CryptoAssetController
{

    public function index()
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1',
            'limit' => '10',
            'convert' => 'EUR'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 56dfac0f-889a-4343-8ee6-787dd7ec6a92'
        ];
        $qs = http_build_query($parameters);
        $request = "{$url}?{$qs}";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        ]);

        $response = json_decode(curl_exec($curl), false);

        curl_close($curl);

        $cryptoAssets = [];

        foreach ($response->data as $asset) {
            $cryptoAssets[] = new CryptoAsset(
                $asset->name,
                $asset->symbol,
                $asset->quote->EUR->price
            );
        }
        return new View('CryptoUserInterface.twig', ['assets' => $cryptoAssets]);
    }
}