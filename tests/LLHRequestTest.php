<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\Exceptions\NCATTimeoutException;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;
use NationalFloodExperts\NCAT\NCAT;

use Exception;
use PHPUnit\Framework\TestCase;

class LLHRequestTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_error_if_required_parameters_are_missing_from_a_LLH_request()
    {
        $validLLHParameters = [
            'lat' => 40.0,
            'lon' => -80.0,
            'inDatum' => 'nad83(1986)',
            'outDatum' => 'nad83(2011)',
        ];

        $ncat = new NCAT();

        foreach (['lat', 'lon', 'inDatum', 'outDatum'] as $requiredParameter) {
            $llhParametersWithMissingRequiredField = array_filter($validLLHParameters, function ($key) use ($requiredParameter) {
                return $key !== $requiredParameter;
            }, ARRAY_FILTER_USE_KEY);

            try {
                $ncat->llhRequest($llhParametersWithMissingRequiredField);
                $this->fail('Failed to assert that the RequiredParameterMissingException was thrown');
            } catch (RequiredParameterMissingException $exception) {
                $this->assertInstanceOf(RequiredParameterMissingException::class, $exception);
            } catch (Exception $exception) {
                $this->fail('Wrong exception thrown ' . $exception->getMessage());
            }
        }
    }

    /** @test */
    public function it_should_throw_an_error_if_the_request_times_out()
    {
        $mockClient = TestingUtils::mockTimeoutRequest();
        $ncat = new NCAT($mockClient);

        $this->expectException(NCATTimeoutException::class);
        $ncat->llhRequest([
            'lat' => 40.0,
            'lon' => -80.0,
            'orthoHt' => 99.0,
            'inDatum' => 'nad83(1986)',
            'outDatum' => 'nad83(2011)',
            'inVertDatum' => 'NGVD29',
            'outVertDatum' => 'NAVD88'
        ]);
    }

    /** @test */
    public function it_should_make_a_LLH_request_if_all_of_the_required_parameters_are_included()
    {
        $mockClient = TestingUtils::mockOKRequest(json_encode($this->mockLLHResponseData()));
        $ncat = new NCAT($mockClient);

        $response = $ncat->llhRequest([
            'lat' => 40.0,
            'lon' => -80.0,
            'orthoHt' => 99.0,
            'inDatum' => 'nad83(1986)',
            'outDatum' => 'nad83(2011)',
            'inVertDatum' => 'NGVD29',
            'outVertDatum' => 'NAVD88'
        ]);

        $this->assertEquals($response->srcLat, 40.0);
        $this->assertEquals($response->srcLon, -80.0);
    }

    private function mockLLHResponseData()
    {
        /*
         * Example request and response taken from the following URL:
         * https://geodesy.noaa.gov/web_services/ncat/lat-long-height-service.shtml
         */
        return [
              "ID" => "1627751399354",
              "nadconVersion" => "5.0",
              "vertconVersion" => "3.0",
              "srcDatum" => "NAD83(1986)",
              "destDatum" => "NAD83(2011)",
              "srcVertDatum" => "N/A",
              "destVertDatum" => "N/A",
              "srcLat" => "40.0000000000",
              "srcLatDms" => "N400000.00000",
              "destLat" => "39.9999983008",
              "destLatDms" => "N395959.99388",
              "deltaLat" => "-0.189",
              "sigLat" => "0.000263",
              "sigLat_m" => "0.0081",
              "srcLon" => "-80.0000000000",
              "srcLonDms" => "W0800000.00000",
              "destLon" => "-79.9999976143",
              "destLonDms" => "W0795959.99141",
              "deltaLon" => "0.204",
              "sigLon" => "0.000221",
              "sigLon_m" => "0.0052",
              "srcEht" => "100.000",
              "destEht" => "N/A",
              "sigEht" => "N/A",
              "srcOrthoht" => "N/A",
              "destOrthoht" => "N/A",
              "sigOrthoht" => "N/A",
              "spcZone" => "PA S-3702",
              "spcNorthing_m" => "76,470.391",
              "spcEasting_m" => "407,886.681",
              "spcNorthing_usft" => "250,886.607",
              "spcEasting_usft" => "1,338,208.220",
              "spcNorthing_ift" => "250,887.109",
              "spcEasting_ift" => "1,338,210.896",
              "spcConvergence" => "-01 27 35.22",
              "spcScaleFactor" => "0.99999024",
              "spcCombinedFactor" => "N/A",
              "utmZone" => "UTM Zone 17",
              "utmNorthing" => "4,428,235.878",
              "utmEasting" => "585,360.668",
              "utmConvergence" => "00 38 34.18",
              "utmScaleFactor" => "0.99968970",
              "utmCombinedFactor" => "N/A",
              "x" => "N/A",
              "y" => "N/A",
              "z" => "N/A",
              "usng" => "17SNE8536028235"
        ];
    }
}
