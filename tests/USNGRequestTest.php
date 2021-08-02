<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\NCAT;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;

use Exception;
use PHPUnit\Framework\TestCase;

class USNGRequestTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_error_if_required_parameters_are_missing_from_a_USNG_request()
    {
        $validUSNGParameters = [
            'usng' => '15SWB4788338641',
            'inDatum' => 'nad83(2011)',
            'outDatum' => 'nad83(NSRS2007)'
        ];

        $ncat = new NCAT();

        foreach (['usng', 'inDatum', 'outDatum'] as $requiredParameter) {
            $usngParametersWithMissingRequiredField = array_filter($validUSNGParameters, function ($key) use ($requiredParameter) {
                return $key !== $requiredParameter;
            }, ARRAY_FILTER_USE_KEY);

            try {
                $ncat->usngRequest($usngParametersWithMissingRequiredField);
                $this->fail('Failed to assert that the RequiredParameterMissingException was thrown');
            } catch (RequiredParameterMissingException $exception) {
                $this->assertInstanceOf(RequiredParameterMissingException::class, $exception);
            } catch (Exception $exception) {
                $this->fail('Wrong exception thrown ' . $exception->getMessage());
            }
        }
    }

    /** @test */
    public function it_should_make_a_USNG_request_if_all_of_the_required_parameters_are_included()
    {
        $mockClient = TestingUtils::mockOKRequest(json_encode($this->mockUSNGResponseData()));
        $ncat = new NCAT($mockClient);

        $response = $ncat->usngRequest([
            'usng' => '15SWB4788338641',
            'inDatum' => 'nad83(2011)',
            'outDatum' => 'nad83(NSRS2007)'
        ]);

        $this->assertEquals($response->utmZone, "UTM Zone 17");
    }

    private function mockUSNGResponseData()
    {
        /*
         * Example request and response taken from the following URL:
         * https://geodesy.noaa.gov/web_services/ncat/usng-service.shtml
         */
        return [
            "ID" => "1569252505661",
            "nadconVersion" => "5.0",
            "vertconVersion" => "3.0",
            "srcDatum" => "NAD83(2011)",
            "destDatum" => "NAD83(2011)",
            "srcVertDatum" => "NGVD29",
            "destVertDatum" => "NAVD88",
            "srcLat" => "40.0000000000",
            "srcLatDms" => "N400000.00000",
            "destLat" => "40.0000000000",
            "destLatDms" => "N400000.00000",
            "sigLat" => "0.000000",
            "srcLon" => "-80.0000000000",
            "srcLonDms" => "W0800000.00000",
            "destLon" => "-80.0000000000",
            "destLonDms" => "W0800000.00000",
            "sigLon" => "0.000000",
            "srcEht" => "N/A",
            "destEht" => "N/A",
            "sigEht" => "N/A",
            "srcOrthoht" => "20.000",
            "destOrthoht" => "19.848",
            "sigOrthoht" => "0.005",
            "spcZone" => "PA S-3702",
            "spcNorthing_m" => "76,470.584",
            "spcEasting_m" => "407,886.482",
            "spcNorthing_usft" => "250,887.243",
            "spcEasting_usft" => "1,338,207.567",
            "spcNorthing_ift" => "250,887.744",
            "spcEasting_ift" => "1,338,210.244",
            "spcConvergence" => "-01 27 35.22",
            "spcScaleFactor" => "0.99999024",
            "spcCombinedFactor" => "N/A",
            "utmZone" => "UTM Zone 17",
            "utmNorthing" => "4,428,236.065",
            "utmEasting" => "585,360.462",
            "utmConvergence" => "00 38 34.17",
            "utmScaleFactor" => "0.99968970",
            "utmCombinedFactor" => "N/A",
            "x" => "N/A",
            "y" => "N/A",
            "z" => "N/A",
            "usng" => "17TNE8536028236"
        ];
    }
}
