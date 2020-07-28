# Rajaongkir
Advanced Rajaongkir API with Guzzle. Support for all types of accounts. PRO, BASIC and STARTER

### Penggunaan
```php
// All provinces
$provinces = $rajaOngkir->getProvinces(); // OK
// province by province ID
$province = $rajaOngkir->getProvince(1); // OK

// All cities
$cities = $rajaOngkir->getCities(21); // OK
// Citites by province ID
$cities = $rajaOngkir->getCities(21); // OK
// City by city ID
$city = $rajaOngkir->getCity(1); // OK

// Subdistricts (kecamatan) by city ID
$getSubdistricts = $rajaOngkir->getSubdistricts(1); // OK
// Subdistricts by subdistrict ID
$getSubdistrict = $rajaOngkir->getSubdistrict(1); // OK

// required for PRO ['origin', 'originType', 'destination', 'destinationType', 'weight', 'courier']
// optional for PRO ['length', 'width', 'height', 'diameter'] in cm
// required for BASIC/STARTER ['origin', 'destination', 'weight', 'courier']
// ex: city to subdistrict (kota ke kecamatan), subdistrict to subdistrict (kecamatan ke kecamatan), 
// atau city to city (kota ke kota).
$getCost = $rajaOngkir->getCost([
    'origin' => 501,
    'originType' => 'city',
    'destination' => 114,
    'destinationType' => 'city',
    'weight' => 400,
    'courier' => 'jne', // for multiple ex: 'jne:tiki:pos'
]); // OK

// param 1 = city ID | param 2: province ID. city ID or province ID is not required
$getInternationalOrigins = $rajaOngkir->getInternationalOrigins(152, 6); // OK

// param: country ID. not required
$getInternationalDestinations = $rajaOngkir->getInternationalDestinations(1); // OK

// not supported for STARTER account. required params ['origin', 'destination', 'weight', 'courier']
// optional params ['length', 'width', 'height]
$getInternationalCost = $rajaOngkir->getInternationalCost([
    'origin' => 152,
    'destination' => 108,
    'weight' => 1400,
    'courier' => 'pos',
]); // OK

// all currency
$getCurrency = $rajaOngkir->getCurrency(); // OK

// param 1: no resi, param 2: courier
$getWaybill = $rajaOngkir->getWaybill('SOCAG00183235715', 'jne'); // OK

|| HELPER ||
// get all courier list
$courier = $rajaOngkir->getCouriersList(); // OK
// get supported courier for STARTER|PRO|BASIC
$getSupportedCouriers = $rajaOngkir->getSupportedCouriers(); // OK
// get supported waybill courier for STARTER|PRO|BASIC
$getSupportedWayBills = $rajaOngkir->getSupportedWayBills(); // OK
```
