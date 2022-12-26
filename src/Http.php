<?php

namespace Pishran\IpPanel;

use Exception;
use Pishran\IpPanel\Models\Response;

class Http
{
    const SUPPORTED_STATUS_CODES = [
        200,
        201,
        204,
        405,
        400,
        404,
        401,
        422,
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
     * @var array<string, string>
     */
    protected $headers;

    /**
     * @param  string  $baseUrl
     * @param  int  $timeout
     * @param  string[]  $headers
     */
    public function __construct(string $baseUrl, int $timeout, array $headers = [])
    {
        $this->baseUrl = $baseUrl;
        $this->timeout = $timeout;
        $this->headers = $headers;
    }

    /**
     * @param  string  $url
     * @param  array<string, mixed>  $params
     * @param  string[]  $headers
     * @return Response
     *
     * @throws Exception
     */
    public function get(string $url, array $params = [], array $headers = []): Response
    {
        return $this->request('GET', $url, [], $params, $headers);
    }

    /**
     * @param  string  $url
     * @param  array<string, mixed>  $data
     * @param  string[]  $headers
     * @return Response
     *
     * @throws Exception
     */
    public function post(string $url, array $data, array $headers = []): Response
    {
        return $this->request('POST', $url, $data, [], $headers);
    }

    /**
     * @param  string  $uri
     * @param  array<string, string>  $params
     * @return string
     */
    protected function getBaseUrl(string $uri, array $params = []): string
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($uri, '/');

        return $params
            ? $url.'?'.http_build_query($params)
            : $url;
    }

    /**
     * @param  string  $method
     * @param  string  $url
     * @param  array<string, string>  $data
     * @param  array<string, string>  $params
     * @param  string[]  $headers
     * @return Response
     *
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

        if (count($headers) < 1) {
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
        if (is_bool($response) || ! $response) {
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
