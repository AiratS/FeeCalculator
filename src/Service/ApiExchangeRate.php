<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ApiExchangeRateException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiExchangeRate
{
    private const FORMAT_JSON = 1;
    private const ENDPOINT_LATEST = '/latest';

    private HttpClientInterface $httpClient;
    private string $domain;
    private string $accessKey;
    private string $baseCurrency;

    public function __construct(HttpClientInterface $httpClient, string $domain, string $accessKey, string $baseCurrency)
    {
        $this->httpClient = $httpClient;
        $this->domain = $domain;
        $this->accessKey = $accessKey;
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * @throws ApiExchangeRateException
     */
    public function getExchangeRateData(): array
    {
        try {
            $url = sprintf('%s%s', $this->domain, self::ENDPOINT_LATEST);
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'access_key' => $this->accessKey,
                    'format' => self::FORMAT_JSON,
                    'base' => $this->baseCurrency,
                ]
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new ApiExchangeRateException('Could not request rates from API.');
            }

            $rates = json_decode($response->getContent(), true);
            if (!$rates) {
                throw new ApiExchangeRateException('Could not decode the API response');
            }

            if (!isset($rates['rates'])) {
                throw new ApiExchangeRateException('Invalid API response.');
            }

            return $rates['rates'];
        } catch (ApiExchangeRateException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            throw new ApiExchangeRateException('Could not request from API.');
        }
    }
}
