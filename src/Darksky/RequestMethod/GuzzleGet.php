<?php

namespace Darksky;

use Exception;
use GuzzleHttp\Client;

/**
 * Class GuzzleGet.
 */
class GuzzleGet implements RequestMethod
{
    private $statusCode = null;
    private $responseHeaders = [];

    /**
     * GuzzleGet constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $requestUrl
     *
     * @throws DarkskyException
     *
     * @return bool|string
     */
    public function submit(string $requestUrl)
    {
        $client = new Client();

        try {
            $response = $client->request('GET', $requestUrl);
        } catch (Exception $e) {
            throw new DarkskyException($e->getMessage());
        }

        $this->statusCode = $response->getStatusCode();
        $this->responseHeaders = $response->getHeaders();

        return (string) $response->getBody();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }
}
