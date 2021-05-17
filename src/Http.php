<?php

namespace Pishran\IpPanel;

use Exception;
use Pishran\IpPanel\Models\Response;

class Http
{
    const SUPPORTED_STATUS_CODES = [
        200, 201, 204, 405, 400, 404, 401, 422,
    ];

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var array
     */
    protected $headers;

    public function __construct(string $baseUrl, int $timeout, array $headers = [])
    {
        $this->baseUrl = $baseUrl;
        $this->timeout = $timeout;
        $this->headers = $headers;
    }

    /**
     * @throws Exception
     */
    public function get(string $url, array $params = [], array $headers = []): Response
    {
        return $this->request('GET', $url, [], $params, $headers);
    }

    /**
     * @throws Exception
     */
    public function post(string $url, array $data, array $headers = []): Response
    {
        return $this->request('POST', $url, $data, [], $headers);
    }

    protected function getBaseUrl(string $uri, array $params = []): string
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($uri, '/');

        return $params
            ? $url.'?'.http_build_query($params)
            : $url;
    }

    /**
     * @throws Exception
     */
    protected function request(
        string $method,
        string $url,
        array $data = [],
        array $params = [],
        array $headers = []
    ): Response {
        $curl = curl_init();

        if (! $headers || count($headers) < 1) {
            $headers = ['Accept: application/json', 'Content-Type: application/json'];
        }

        $headers = array_merge($headers, $this->headers);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $this->getBaseUrl($url, $params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, @json_encode($data));
        } else {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }

        $response = curl_exec($curl);
        if ($response === false) {
            throw new Exception(curl_error($curl), curl_errno($curl));
        }

        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (! in_array($status, self::SUPPORTED_STATUS_CODES)) {
            throw new Exception('Unexpected HTTP error occurred.', $status);
        }

        $arrayResponse = json_decode($response);

        $parsedResponse = new Response;
        $parsedResponse->fromJson($arrayResponse);

        if (isset($parsedResponse->data) && isset($parsedResponse->data->error)) {
            throw new Exception($parsedResponse->data->error, $parsedResponse->code);
        }

        return $parsedResponse;
    }
}
