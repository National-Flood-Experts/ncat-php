<?php

namespace NationalFloodExperts\NCAT;

use NationalFloodExperts\NCAT\Exceptions\CannotConnectToNCATException;
use NationalFloodExperts\NCAT\Exceptions\NCATTimeoutException;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class NCAT
{
    const BASE_URI = 'https://geodesy.noaa.gov/api/ncat/';
    const CONNECT_TIMEOUT = 6;
    const TIMEOUT = 10;

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => self::BASE_URI,
            'connect_timeout' => self::CONNECT_TIMEOUT,
            'timeout' => self::TIMEOUT
        ]);
    }

    public function llhRequest(array $parameters = [])
    {
        $requiredLLHParameters = ['lat', 'lon', 'inDatum', 'outDatum'];
        return $this->makeRequest('llh', $requiredLLHParameters, $parameters);
    }

    public function spcRequest(array $parameters = [])
    {
        $requiredSPCParameters = ['northing', 'easting', 'inDatum', 'outDatum', 'spcZone'];
        return $this->makeRequest('spc', $requiredSPCParameters, $parameters);
    }

    public function utmRequest(array $parameters = [])
    {
        /*
         * According to the NCAT documentation, the "spcZone" parameter is
         * required for a UTM request. However, requests can be made without
         * it. Since the documentation says that it is requried, we will check
         * for it before making a request.
         *
         * More information below:
         * https://geodesy.noaa.gov/web_services/ncat/utm-service.shtml
         */
        $requiredUTMParameters = ['northing', 'easting', 'inDatum', 'outDatum', 'spcZone', 'utmZone'];
        return $this->makeRequest('utm', $requiredUTMParameters, $parameters);
    }

    public function xyzRequest(array $parameters = [])
    {
        $requiredXYZParameters = ['x', 'y', 'z', 'inDatum', 'outDatum'];
        return $this->makeRequest('xyz', $requiredXYZParameters, $parameters);
    }

    public function usngRequest(array $parameters = [])
    {
        $requiredUSNGParameters = ['usng', 'inDatum', 'outDatum'];
        return $this->makeRequest('usng', $requiredUSNGParameters, $parameters);
    }

    public function makeRequest($endpoint, $requiredParameters, $givenParameters)
    {
        $this->checkForRequiredParameters($requiredParameters, $givenParameters);

        try {
            $queryString = $this->buildQueryString($givenParameters);
            $response = $this->client->get("$endpoint?$queryString");
            return json_decode($response->getBody());
        } catch (ConnectException $exception) {
            throw new CannotConnectToNCATException('The connection with the NCAT server could not be established');
        } catch (RequestException $exception) {
            if (in_array($exception->getResponse()->getStatusCode(), ['408'])) {
                throw new NCATTimeoutException();
            }
            throw $exception;
        }
    }

    public function checkForRequiredParameters($requiredParameters, $parameters)
    {
        foreach ($requiredParameters as $required) {
            if (!array_key_exists($required, $parameters)) {
                throw new RequiredParameterMissingException($required);
            }
        }
    }

    public function buildQueryString(array $parameters): string
    {
        $queryString = '';

        foreach ($parameters as $key => $value) {
            $queryString .= $key . '=' . $value . '&';
        }

        return substr($queryString, 0, -1);
    }
}
