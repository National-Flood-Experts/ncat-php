<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\NCAT;
use NationalFloodExperts\NCAT\Exceptions\RequiredParameterMissingException;

use Exception;
use PhpUnit\Framework\TestCase;

class SPCRequestTest extends TestCase
{
    /** @test */
    public function it_should_throw_an_error_if_required_parameters_are_missing_from_a_SPC_request()
    {
        $validSPCParameters = [
            'northing' => 173099.419,
            'easting' => 503626.812,
            'spcZone' => 2402,
            'inDatum' => 'nad83(2011)',
            'outDatum' => 'nad83(NSRS2007)'
        ];

        $ncat = new NCAT();

        foreach (['northing', 'easting', 'spcZone', 'inDatum', 'outDatum'] as $requiredParameter) {
            $spcParametersWithMissingRequiredField = array_filter($validSPCParameters, function ($key) use ($requiredParameter) {
                return $key !== $requiredParameter;
            }, ARRAY_FILTER_USE_KEY);

            try {
                $ncat->spcRequest($spcParametersWithMissingRequiredField);
                $this->fail('Failed to assert that the RequiredParameterMissingException was thrown');
            } catch (RequiredParameterMissingException $exception) {
                $this->assertInstanceOf(RequiredParameterMissingException::class, $exception);
            } catch (Exception $exception) {
                $this->fail('Wrong exception thrown ' . $exception->getMessage());
            }
        }
    }

    /** @test */
    public function it_should_make_a_SPC_request_if_all_of_the_required_parameters_are_included()
    {
        $mockClient = TestingUtils::mockOKRequest(json_encode($this->mockSPCResponseData()));
        $ncat = new NCAT($mockClient);

        $response = $ncat->spcRequest([
            'northing' => 173099.419,
            'easting' => 503626.812,
            'spcZone' => 2402,
            'inDatum' => 'nad83(2011)',
            'outDatum' => 'nad83(NSRS2007)'
        ]);

        $this->assertEquals($response->srcDatum, 'NAD83(2011)');
        $this->assertEquals($response->destDatum, 'NAD83(NSRS2007)');
    }

    private function mockSPCResponseData()
    {
        /*
         * Example request and response taken from the following URL:
         * https://geodesy.noaa.gov/web_services/ncat/spc-service.shtml
         */
        return [
            "ID" => "1627776000708",
            "nadconVersion" => "5.0",
            "vertconVersion" => "3.0",
            "srcDatum" => "NAD83(2011)",
            "destDatum" => "NAD83(NSRS2007)",
            "srcVertDatum" => "N/A",
            "destVertDatum" => "N/A",
            "srcLat" => "37.3933000033",
            "srcLatDms" => "N372335.88001",
            "destLat" => "37.3933000245",
            "destLatDms" => "N372335.88009",
            "deltaLat" => "0.002",
            "sigLat" => "0.000002",
            "sigLat_m" => "0.0001",
            "srcLon" => "-92.4590399988",
            "srcLonDms" => "W0922732.54400",
            "destLon" => "-92.4590401909",
            "destLonDms" => "W0922732.54469",
            "deltaLon" => "-0.017",
            "sigLon" => "0.000003",
            "sigLon_m" => "0.0001",
            "srcEht" => "100.000",
            "destEht" => "100.022",
            "sigEht" => "0.001",
            "srcOrthoht" => "N/A",
            "destOrthoht" => "N/A",
            "sigOrthoht" => "N/A",
            "spcZone" => "MO C-2402",
            "spcNorthing_m" => "173,099.421",
            "spcEasting_m" => "503,626.795",
            "spcNorthing_usft" => "567,910.352",
            "spcEasting_usft" => "1,652,315.577",
            "spcNorthing_ift" => "567,911.487",
            "spcEasting_ift" => "1,652,318.881",
            "spcConvergence" => "00 01 29.55",
            "spcScaleFactor" => "0.99993350",
            "spcCombinedFactor" => "0.99991781",
            "utmZone" => "UTM Zone 15",
            "utmNorthing" => "4,138,641.149",
            "utmEasting" => "547,883.621",
            "utmConvergence" => "00 19 42.68",
            "utmScaleFactor" => "0.99962824",
            "utmCombinedFactor" => "0.99961255",
            "x" => "-217,687.314",
            "y" => "-5,069,012.437",
            "z" => "3,852,223.079",
            "usng" => "15SWB4788338641"
        ];
    }
}
