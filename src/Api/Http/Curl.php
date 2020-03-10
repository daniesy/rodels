<?php


namespace Daniesy\Rodels\Api\Http;


class Curl extends HttpClient
{

    public function run(string $method, string $url, array $parameters = [], array $headers = []): array
    {
        $this->errors = null;

        $curl = curl_init();

        // Merge global and request headers
        $headers = array_merge(array_values($this->headers), $headers);

        // Set options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HEADER => 1,
            CURLINFO_HEADER_OUT => 1,
            CURLOPT_VERBOSE => 1,
        ]);

        // Setup method specific options
        switch ($method) {
            case 'PUT':
            case 'PATCH':
            case 'POST':
                curl_setopt_array($curl, [
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $this->pack($parameters),
                ]);
                break;

            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
        }

        // Make request
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response = curl_exec($curl);

        // Set HTTP response code
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Set errors if there are any
        if (curl_errno($curl)) {
            $this->errors = curl_error($curl);
        }

        // Parse body
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        return [$body, $this->parseHeaders($header)];
    }

    public function pack(array $params)
    {
        return json_encode($params);
    }

}
