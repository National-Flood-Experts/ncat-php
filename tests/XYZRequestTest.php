<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\NCAT;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;

use Exception;
use PHPUnit\Framework\TestCase;

class XYZRequestTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_error_if_required_parameters_are_missing_from_a_XYZ_request()
    {
        $validXYZParameters = [
            'x' => -217687.279,
            'y' => -5069012.406,
            'z' => 3852223.048,
            'inDatum' => 'NAD83(2011)',
            'outDatum' => 'NAD83(NSRS2007)'
        ];

        $ncat = new NCAT();

        foreach (['x', 'y', 'z', 'inDatum', 'outDatum'] as $requiredParameter) {
            $xyzParametersWithMissingRequiredField = array_filter($validXYZParameters, function ($key) use ($requiredParameter) {
                return $key !== $requiredParameter;
            }, ARRAY_FILTER_USE_KEY);

            try {
                $ncat->xyzRequest($xyzParametersWithMissingRequiredField);
                $this->fail('Failed to assert that the RequiredParameterMissingException was thrown');
            } catch (RequiredParameterMissingException $exception) {
                $this->assertInstanceOf(RequiredParameterMissingException::class, $exception);
            } catch (Exception $exception) {
                $this->fail('Wrong exception thrown ' . $exception->getMessage());
            }
        }
    }

    /** @test */
    public function it_should_make_a_XYZ_request_if_all_of_the_required_parameters_are_included()
    {
        $mockClient = TestingUtils::mockOKRequest(json_encode($this->mockXYZResponseData()));
        $ncat = new NCAT($mockClient);

        $response = $ncat->xyzRequest([
            'x' => -217687.279,
            'y' => -5069012.406,
            'z' => 3852223.048,
            'inDatum' => 'NAD83(2011)',
            'outDatum' => 'NAD83(NSRS2007)'
        ]);

        $this->assertEquals($response->x, "-217,687.297");
        $this->assertEquals($response->y, "-5,069,012.421");
        $this->assertEquals($response->z, "3,852,223.063");
    }

    private function mockXYZResponseData()
    {
        /*
         * Example request and response taken from the following URL:
         * https://geodesy.noaa.gov/web_services/ncat/xyz-service.shtml
         */
        return [
            "ID" => "1627777832702",
            "nadconVersion" => "5.0",
            "vertconVersion" => "3.0",
            "srcDatum" => "NAD83(2011)",
            "destDatum" => "NAD83(NSRS2007)",
            "srcVertDatum" => "N/A",
            "destVertDatum" => "N/A",
            "srcLat" => "37.3932999796",
            "srcLatDms" => "N372335.87993",
            "destLat" => "37.3933000008",
            "destLatDms" => "N372335.88000",
            "deltaLat" => "0.002",
            "sigLat" => "0.000002",
            "sigLat_m" => "0.0001",
            "srcLon" => "-92.4590398118",
            "srcLonDms" => "W0922732.54332",
            "destLon" => "-92.4590400039",
            "destLonDms" => "W0922732.54401",
            "deltaLon" => "-0.017",
            "sigLon" => "0.000003",
            "sigLon_m" => "0.0001",
            "srcEht" => "99.978",
            "destEht" => "100.000",
            "sigEht" => "0.001",
            "srcOrthoht" => "N/A",
            "destOrthoht" => "N/A",
            "sigOrthoht" => "N/A",
            "spcZone" => "MO C-2402",
            "spcNorthing_m" => "173,099.419",
            "spcEasting_m" => "503,626.812",
            "spcNorthing_usft" => "567,910.343",
            "spcEasting_usft" => "1,652,315.631",
            "spcNorthing_ift" => "567,911.479",
            "spcEasting_ift" => "1,652,318.936",
            "spcConvergence" => "00 01 29.55",
            "spcScaleFactor" => "0.99993350",
            "spcCombinedFactor" => "0.99991781",
            "utmZone" => "UTM Zone 15",
            "utmNorthing" => "4,138,641.146",
            "utmEasting" => "547,883.638",
            "utmConvergence" => "00 19 42.68",
            "utmScaleFactor" => "0.99962824",
            "utmCombinedFactor" => "0.99961255",
            "x" => "-217,687.297",
            "y" => "-5,069,012.421",
            "z" => "3,852,223.063",
            "usng" => "15SWB4788338641"
        ];
    }
}
