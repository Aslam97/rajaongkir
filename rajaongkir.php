<?php

namespace App;

use GuzzleHttp\Client as GuzzleClient;

class Rajaongkir
{
    const STARTER = 'starter';
    const BASIC = 'basic';
    const PRO = 'pro';

    const POST_REQUEST = 'POST';
    const GET_REQUEST = 'GET';

    protected $accountType;

    /**
     * supportedCouriers
     *
     * @var array
     */
    protected $supportedCouriers = [
        'starter' => [
            'jne',
            'pos',
            'tiki',
        ],
        'basic' => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'esl',
            'rpx',
        ],
        'pro' => [
            'jne',
            'pos',
            'tiki',
            'rpx',
            'esl',
            'pcp',
            'pandu',
            'wahana',
            'sicepat',
            'jnt',
            'pahala',
            'cahaya',
            'sap',
            'jet',
            'indah',
            'slis',
            'expedito*',
            'dse',
            'first',
            'ncs',
            'star',
            'lion',
            'ninja-express',
            'idl',
            'rex',
        ],
    ];

    /**
     * supportedWayBills
     *
     * @var array
     */
    protected $supportedWayBills = [
        'starter' => [],
        'basic' => [
            'jne',
        ],
        'pro' => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'rpx',
            'wahana',
            'sicepat',
            'j&t',
            'sap',
            'jet',
            'dse',
            'first',
        ],
    ];

    /**
     * couriersList
     *
     * @var array
     */
    protected $couriersList = [
        'jne' => 'Jalur Nugraha Ekakurir (JNE)',
        'pos' => 'POS Indonesia (POS)',
        'tiki' => 'Citra Van Titipan Kilat (TIKI)',
        'pcp' => 'Priority Cargo and Package (PCP)',
        'esl' => 'Eka Sari Lorena (ESL)',
        'rpx' => 'RPX Holding (RPX)',
        'pandu' => 'Pandu Logistics (PANDU)',
        'wahana' => 'Wahana Prestasi Logistik (WAHANA)',
        'sicepat' => 'SiCepat Express (SICEPAT)',
        'j&t' => 'J&T Express (J&T)',
        'pahala' => 'Pahala Kencana Express (PAHALA)',
        'cahaya' => 'Cahaya Logistik (CAHAYA)',
        'sap' => 'SAP Express (SAP)',
        'jet' => 'JET Express (JET)',
        'indah' => 'Indah Logistic (INDAH)',
        'slis' => 'Solusi Express (SLIS)',
        'expedito*' => 'Expedito*',
        'dse' => '21 Express (DSE)',
        'first' => 'First Logistics (FIRST)',
        'ncs' => 'Nusantara Card Semesta (NCS)',
        'star' => 'Star Cargo (STAR)',
    ];

    /**
     * __construct
     *
     * @param  mixed $accountType
     * @return void
     */
    public function __construct($accountType = 'starter')
    {
        $this->accountType = $accountType;
    }

    /**
     * request
     *
     * @param  mixed $path
     * @param  mixed $params
     * @param  mixed $type
     * @return void
     */
    protected function request($path, $params = [], $type = self::GET_REQUEST)
    {
        $apiUrl = 'https://api.rajaongkir.com/' . $this->accountType . '/';

        if ($this->accountType === self::PRO) {
            $apiUrl = 'https://pro.rajaongkir.com/api/';
        }

        $config = [
            'base_uri' => $apiUrl,
            'headers' => [
                'key' => config('rajaongkir.api_key'),
            ],
        ];

        if ($type === self::POST_REQUEST) {
            $config['headers']['content-type'] = 'application/x-www-form-urlencoded';
            $config['form_params'] = $params;
        } else {
            $config['query'] = $params;
        }

        $client = new GuzzleClient($config);

        $response = $client->request($type, $path, ['http_errors' => false]);
        $result = json_decode($response->getBody(), true);

        // if null
        if (!$result) {
            return $this->formattedErrors(400, 'No Data.');
        }

        return $result;
    }

    /**
     * Menmpilan semua provinsi
     *
     * @return void
     */
    public function getProvinces()
    {
        return $this->request('province');
    }

    /**
     * Menampilkan provinsi berdasarkan id provinsi
     *
     * @param  mixed $id
     * @return void
     */
    public function getProvince($id)
    {
        return $this->request('province', compact('id'));
    }

    /**
     * Menampilkan semua kota
     * jika $province di isi maka menampilkan kota berdasarkan id provinsi
     *
     * @param  mixed $province
     * @return void
     */
    public function getCities($province = null)
    {
        return $this->request('city', compact('province'));
    }

    /**
     * Menampilkan kota berdasarkan id kota
     *
     * @param  mixed $id
     * @return void
     */
    public function getCity($id)
    {
        return $this->request('city', compact('id'));
    }

    /**
     * Menampilkan kecamatan berdasarkan id kota
     *
     * @param  mixed $id
     * @return void
     */
    public function getSubdistricts($id)
    {
        if ($this->accountType === self::STARTER || $this->accountType === self::BASIC) {
            return $this->formattedErrors(
                302,
                'Unsupported Subdistricts Request. Tipe akun ' . $this->accountType . ' tidak mendukung hingga tingkat kecamatan.'
            );
        }

        return $this->request('subdistrict', compact('id'));
    }

    /**
     * Menampilkan kecamatan berdasarkan id kecamatan
     *
     * @param  mixed $id
     * @return void
     */
    public function getSubdistrict($id)
    {
        if ($this->accountType === self::STARTER || $this->accountType === self::BASIC) {
            return $this->formattedErrors(
                302,
                'Unsupported Subdistricts Request. Tipe akun ' . $this->accountType . ' tidak mendukung hingga tingkat kecamatan.'
            );
        }

        return $this->request('subdistrict', compact('id'));
    }

    /**
     * getCost
     *
     * @param  mixed $params
     * @return void
     */
    public function getCost($params = [])
    {
        $requiredKeys = ['origin', 'destination', 'weight', 'courier'];

        if ($this->accountType === self::PRO) {
            array_push($requiredKeys, 'originType', 'destinationType');
        }

        $paramKeys = array_keys($params);
        $missingRequiredKeys = array_diff($requiredKeys, $paramKeys);

        if (!empty($missingRequiredKeys)) {

            $toString = implode(',', $missingRequiredKeys);
            return $this->formattedErrors(
                400,
                'Missing ' . $toString . ' fields for account type ' . $this->accountType
            );
        }

        return $this->request('cost', $params, self::POST_REQUEST);
    }

    /**
     * Mendapatkan daftar/nama kota yang mendukung pengiriman internasional.
     * Jika ID kota dan ID propinsi kosong,
     * maka akan menampilkan semua kota/kabupaten yang mendukung pengiriman internasional di Indonesia.
     *
     * @return void
     */
    public function getInternationalOrigins($id = null, $province = null)
    {
        if ($this->accountType === self::STARTER) {
            return $this->formattedErrors(
                302,
                'Unsupported International Origin Request. Tipe akun ' . $this->accountType . ' tidak mendukung tingkat international.'
            );
        }

        return $this->request('v2/internationalOrigin', compact('id', 'province'));
    }

    /**
     * getInternationalDestinations
     * Jika ID negara kosong, maka akan menampilkan semua negara tujuan pengiriman internasional.
     *
     * @param  mixed $id as country id
     * @return void
     */
    public function getInternationalDestinations($id = null)
    {
        if ($this->accountType === self::STARTER) {
            return $this->formattedErrors(
                302,
                'Unsupported International Destination Request. Tipe akun ' . $this->accountType . ' tidak mendukung tingkat international.'
            );
        }

        return $this->request('v2/internationalDestination', compact('id'));
    }

    /**
     * getInternationalCost
     *
     * @param  mixed $params
     * @return void
     */
    public function getInternationalCost($params = [])
    {
        if ($this->accountType === self::STARTER) {
            return $this->formattedErrors(
                302,
                'Unsupported International Cost Request. Tipe akun ' . $this->accountType . ' tidak mendukung tingkat international.'
            );
        }

        $requiredKeys = ['origin', 'destination', 'weight', 'courier'];

        $paramKeys = array_keys($params);
        $missingRequiredKeys = array_diff($requiredKeys, $paramKeys);

        if (!empty($missingRequiredKeys)) {

            $toString = implode(',', $missingRequiredKeys);
            return $this->formattedErrors(
                400,
                'Missing ' . $toString . ' fields for account type ' . $this->accountType
            );
        }

        return $this->request('v2/internationalCost', $params, self::POST_REQUEST);
    }

    /**
     * getCurrency
     * Mendapatkan informasi nilai tukar rupiah terhadap US dollar
     *
     * @return void
     */
    public function getCurrency()
    {
        if ($this->accountType === self::STARTER) {
            return $this->formattedErrors(
                302,
                'Unsupported Currency Request. Tipe akun ' . $this->accountType . ' tidak mendukung pengecekan currency.'
            );
        }

        return $this->request('currency');
    }

    /**
     * getWaybill
     * melacak/mengetahui status pengiriman berdasarkan nomor resi
     *
     * @param  mixed $waybill
     * @param  mixed $courier
     * @return void
     */
    public function getWaybill($waybill, $courier)
    {
        if ($this->accountType === self::STARTER) {
            return $this->formattedErrors(
                302,
                'Unsupported Waybill Request. Tipe akun ' . $this->accountType . ' tidak mendukung melacak/mengetahui status pengiriman.'
            );
        }

        $supportedWayBills = $this->supportedWayBills[$this->accountType];
        $isCourierSupport = in_array($courier, $supportedWayBills);

        if (!$isCourierSupport) {
            return $this->formattedErrors(
                400,
                'Unsupportted courier for account type ' . $this->accountType . ' check supported getSupportedWayBills.'
            );
        }

        return $this->request('waybill', compact('waybill', 'courier'), self::POST_REQUEST);
    }

    /**
     * || HELPER ||
     */

    /**
     * getSupportedCouriers
     *
     * @return void
     */
    public function getSupportedCouriers()
    {
        return $this->supportedCouriers[$this->accountType];
    }

    /**
     * getSupportedWayBills
     *
     * @return void
     */
    public function getSupportedWayBills()
    {
        return $this->supportedWayBills[$this->accountType];
    }

    /**
     * Menampilkan list kurir
     *
     * @return void
     */
    public function getCouriersList()
    {
        return $this->couriersList;
    }

    /**
     * formattedErrors
     *
     * @param  mixed $statusCode
     * @param  mixed $description
     * @return void
     */
    private function formattedErrors($statusCode, $description)
    {
        return [
            'rajaongkir' => [
                'status' => [
                    'code' => $statusCode,
                    'description' => $description,
                ],
            ],
        ];
    }
}
