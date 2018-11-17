<?php

namespace Lacuna\RestPki\Legacy;

use Httpful\Request;
use Httpful\Exception\ConnectionErrorException;
use Httpful\Response;

class RestPkiClient
{
    const LIB_VERSION = '1.0.0';

    private $endpointUrl;
    private $accessToken;

    public function __construct($endpointUrl, $accessToken)
    {
        $this->endpointUrl = $endpointUrl;
        $this->accessToken = $accessToken;
    }

    public function get($url)
    {
        $verb = 'GET';
        $request = Request::get($this->endpointUrl . $url)
            ->expectsJson()
            ->addHeader('Authorization', 'Bearer ' . $this->accessToken)
            ->addHeader('X-RestPki-Client', 'PHP Legacy ' . RestPkiClient::LIB_VERSION);
        try {
            $httpResponse = $request->send();
        } catch (ConnectionErrorException $ex) {
            throw new RestUnreachableException($verb, $url, $ex);
        }
        $this->checkResponse($verb, $url, $httpResponse);
        return $httpResponse->body;
    }

    public function post($url, $data)
    {
        $verb = 'POST';
        $request = Request::post($this->endpointUrl . $url)
            ->expectsJson()
            ->addHeader('Authorization', 'Bearer ' . $this->accessToken)
            ->addHeader('X-RestPki-Client', 'PHP Legacy ' . RestPkiClient::LIB_VERSION);
        if (!is_null($data)) {
            $request->sendsJson()->body(json_encode($data));
        }
        try {
            $httpResponse = $request->send();
        } catch (ConnectionErrorException $ex) {
            throw new RestUnreachableException($verb, $url, $ex);
        }
        $this->checkResponse($verb, $url, $httpResponse);
        return $httpResponse->body;
    }

    private function checkResponse($verb, $url, Response $httpResponse)
    {
        $statusCode = $httpResponse->code;
        if ($statusCode < 200 || $statusCode > 299) {
            $ex = null;
            try {
                $response = $httpResponse->body;
                if ($statusCode == 422 && !empty($response->code)) {
                    if ($response->code == "ValidationError") {
                        $vr = new ValidationResults($response->validationResults);
                        $ex = new ValidationException($verb, $url, $vr);
                    } else {
                        $ex = new RestPkiException($verb, $url, $response->code, $response->detail);
                    }
                } else {
                    $ex = new RestErrorException($verb, $url, $statusCode, $response->message);
                }
            } catch (\Exception $e) {
                $ex = new RestErrorException($verb, $url, $statusCode);
            }
            throw $ex;
        }
    }

    /**
     * @internal
     * @param $url
     * @return string
     * @throws RestErrorException
     * @throws RestPkiException
     * @throws RestUnreachableException
     * @throws ValidationException
     */
    public function _downloadContent($url)
    {
        $verb = 'GET';
        $request = Request::get($this->endpointUrl . $url)
            ->addHeaders(array(
                'Authorization' => sprintf('Bearer %s', $this->accessToken),
                'X-RestPki-Client' => sprintf('PHP Legacy %s', RestPkiClient::LIB_VERSION)
            ));
        try {
            $httpResponse = $request->send();
        } catch (ConnectionErrorException $ex) {
            throw new RestUnreachableException($verb, $url, $ex);
        }
        $this->checkResponse($verb, $url, $httpResponse);
        return $httpResponse->body;
    }

    public function getAuthentication()
    {
        return new Authentication($this);
    }
}