# NCAT-PHP

This is a simple implementation for [NOAA's NCAT API Service.](https://geodesy.noaa.gov/web_services/ncat/index.shtml) You can learn more about the National Geodetic Survey from NOAA [here.](https://www.ngs.noaa.gov/)

Here is a simple example on how to use the package.

```php
$ncat = new NationalFloodExperts\NCAT\NCAT();

// Make a request to the Latitude-longitude-height service
$data = $ncat->llhRequest([
  'lat' => 40.0,
  'lon' => -80.0,
  'eht' => 100.0,
  'inDatum' => 'NAD83(1986)',
  'outDatum' => 'NAD83(2011)'
]);

// Make a request to the U.S. National Grid service
$data = $ncat->usngRequest([
  'usng' => '15SWB4788338641',
  'inDatum' => 'NAD83(2011)',
  'outDatum' => 'NAD83(NSRS2007)',
  'eht' => 100.0
]);
```
