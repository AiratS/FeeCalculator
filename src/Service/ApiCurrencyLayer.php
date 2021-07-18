<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ApiCurrencyLayerException;

class ApiCurrencyLayer
{
    const FORMAT_JSON = 1;
    const ENDPOINT_LIVE = '/live';

    /**
     * @var string
     */
    private string $domain;

    /**
     * @var string
     */
    private string $accessKey;

    /**
     * @param string $domain
     * @param string $accessKey
     */
    public function __construct(string $domain, string $accessKey)
    {
        $this->domain = $domain;
        $this->accessKey = $accessKey;
    }

    /**
     * @return array
     * @throws ApiCurrencyLayerException
     */
    public function getExchangeRateData(): array
    {
        $params = http_build_query([
            'access_key' => $this->accessKey,
            'format' => self::FORMAT_JSON,
        ]);
        $url = sprintf('%s%s?%s', $this->domain, self::ENDPOINT_LIVE, $params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);

        if (false === $response) {
            throw new ApiCurrencyLayerException('There is no response from API.');
        }

        $rates = json_decode($response, true);
        if (null === $rates) {
            throw new ApiCurrencyLayerException('Could not decode the API response');
        }

        if (!isset($rates['quotes'])) {
            throw new ApiCurrencyLayerException('Invalid API response.');
        }

        return $rates['quotes'];
    }
}
