<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ApiCurrencyLayerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiCurrencyLayer
{
    /**
     * In the free version of the API, exchange rates are shown only against the dollar
     */
    public const BASE_CURRENCY = 'USD';
    private const FORMAT_JSON = 1;
    private const ENDPOINT_LIVE = '/live';

    private HttpClientInterface $httpClient;
    private string $domain;
    private string $accessKey;

    public function __construct(HttpClientInterface $httpClient, string $domain, string $accessKey)
    {
        $this->httpClient = $httpClient;
        $this->domain = $domain;
        $this->accessKey = $accessKey;
    }

    /**
     * @throws ApiCurrencyLayerException
     */
    public function getExchangeRateData(): array
    {
        try {
            $url = $this->domain . self::ENDPOINT_LIVE;
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'access_key' => $this->accessKey,
                    'format' => self::FORMAT_JSON,
                    'source' => self::BASE_CURRENCY,
                ]
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new ApiCurrencyLayerException('Could not request rates from API.');
            }

            $rates = json_decode($response->getContent(), true);
            if (!$rates) {
                throw new ApiCurrencyLayerException('Could not decode the API response');
            }

            if (!isset($rates['quotes'])) {
                throw new ApiCurrencyLayerException('Invalid API response.');
            }

            return $rates['quotes'];
        } catch (ApiCurrencyLayerException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            throw new ApiCurrencyLayerException('Could not request from API.');
        }
    }
}
