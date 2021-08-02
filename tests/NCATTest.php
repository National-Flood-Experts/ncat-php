<?php

namespace NationalFloodExperts\NCAT\Test;

use NationalFloodExperts\NCAT\NCAT;

use PHPUnit\Framework\TestCase;

class NCATTest extends TestCase
{
    /** @test */
    public function it_should_take_any_parameters_and_convert_them_to_a_query_string()
    {
        $ncat = new NCAT();
        $result = $ncat->buildQueryString([
            'lat' => 39.2240867222,
            'lon' => -98.5421515000,
            'orthoHt' => 100.0,
            'inDatum' => 'NAD83(1986)',
            'outDatum' => 'NAD83(2011)',
            'inVertDatum' => 'NGVD29',
            'outVertDatum' => 'NAVD88'
        ]);

        $this->assertEquals(
            $result,
            'lat=39.2240867222&lon=-98.5421515&orthoHt=100&inDatum=NAD83(1986)&outDatum=NAD83(2011)&inVertDatum=NGVD29&outVertDatum=NAVD88'
        );
    }
}
