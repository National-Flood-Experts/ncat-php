<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\NCAT;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;

use Exception;
use PHPUnit\Framework\TestCase;

class UTMRequestTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_error_if_required_parameters_are_missing_from_a_UTM_request()
    {
        $validUTMParameters = [
            'northing' => 4138641.144,
            'easting' => 547883.655,
            'utmZone' => 15,
            'spcZone' => 2401,
            'inDatum' => 'NAD83(2011)',
            'outDatum' => 'NAD83(NSRS2007)'
        ];

        $ncat = new NCAT();

        foreach (['northing', 'easting', 'spcZone', 'inDatum', 'outDatum', 'utmZone'] as $requiredParameter) {
            $utmParametersWithMissingRequiredField = array_filter($validUTMParameters, function ($key) use ($requiredParameter) {
                return $key !== $requiredParameter;
            }, ARRAY_FILTER_USE_KEY);

            try {
                $ncat->utmRequest($utmParametersWithMissingRequiredField);
                $this->fail('Failed to assert that the RequiredParameterMissingException was thrown');
            } catch (RequiredParameterMissingException $exception) {
                $this->assertInstanceOf(RequiredParameterMissingException::class, $exception);
            } catch (Exception $exception) {
                $this->fail('Wrong exception thrown ' . $exception->getMessage());
            }
        }
    }

    /** @test */
    public function it_should_make_a_UTM_request_if_all_of_the_required_parameters_are_included()
    {
        $mockClient = TestingUtils::mockOKRequest(json_encode($this->mockUTMResponseData()));
        $ncat = new NCAT($mockClient);

        $response = $ncat->utmRequest([
            'northing' => 4138641.144,
            'easting' => 547883.655,
            'utmZone' => 15,
            'spcZone' => 2401,
            'inDatum' => 'NAD83(2011)',
            'outDatum' => 'NAD83(NSRS2007)'
        ]);

        $this->assertEquals($response->x, "-217,687.297");
        $this->assertEquals($response->y, "-5,069,012.439");
        $this->assertEquals($response->z, "3,852,223.077");
    }

    private function mockUTMResponseData()
    {
        /*
         * Example request and response taken from the following URL:
         * https://geodesy.noaa.gov/web_services/ncat/utm-service.shtml
         */
        return [
          "ID" => "1627777267747",
          "nadconVersion" => "5.0",
          "vertconVersion" => "3.0",
          "srcDatum" => "NAD83(2011)",
          "destDatum" => "NAD83(NSRS2007)",
          "srcVertDatum" => "N/A",
          "destVertDatum" => "N/A",
          "srcLat" => "37.3932999809",
          "srcLatDms" => "N372335.87993",
          "destLat" => "37.3933000021",
          "destLatDms" => "N372335.88001",
          "deltaLat" => "0.002",
          "sigLat" => "0.000002",
          "sigLat_m" => "0.0001",
          "srcLon" => "-92.4590398075",
          "srcLonDms" => "W0922732.54331",
          "destLon" => "-92.4590399996",
          "destLonDms" => "W0922732.54400",
          "deltaLon" => "-0.017",
          "sigLon" => "0.000003",
          "sigLon_m" => "0.0001",
          "srcEht" => "100.000",
          "destEht" => "100.022",
          "sigEht" => "0.001",
          "srcOrthoht" => "N/A",
          "destOrthoht" => "N/A",
          "sigOrthoht" => "N/A",
          "spcZone" => "MO E-2401",
          "spcNorthing_m" => "174,900.027",
          "spcEasting_m" => "76,527.436",
          "spcNorthing_usft" => "573,817.839",
          "spcEasting_usft" => "251,073.762",
          "spcNorthing_ift" => "573,818.987",
          "spcEasting_ift" => "251,074.264",
          "spcConvergence" => "-01 11 23.96",
          "spcScaleFactor" => "1.00030390",
          "spcCombinedFactor" => "1.00028820",
          "utmZone" => "UTM Zone 15",
          "utmNorthing" => "4,138,641.146",
          "utmEasting" => "547,883.638",
          "utmConvergence" => "00 19 42.68",
          "utmScaleFactor" => "0.99962824",
          "utmCombinedFactor" => "0.99961255",
          "x" => "-217,687.297",
          "y" => "-5,069,012.439",
          "z" => "3,852,223.077",
          "usng" => "15SWB4788338641"
        ];
    }
}
