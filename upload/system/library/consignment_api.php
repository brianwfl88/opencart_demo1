<?php
class ConsignmentApi {
    protected $app_id; // initialize from $this->initializeConsignmentSetting()
    protected $app_key; // initialize from $this->initializeConsignmentSetting()
    protected $consignment_prefix; // initialize from $this->initializeConsignmentSetting()
    protected $con_no = 1; // initialize from $this->initializeConsignmentNumber()
    protected $store_id = 0;

	private $api_server = 'https://smartedi.my.kerryexpress.com/';

	public function __construct($registry) {
        $this->registry = $registry;
        
		$this->config = $registry->get('config');
        $this->db = $registry->get('db');

        $this->initializeConsignmentSetting();
        $this->initializeConsignmentNumber();
    }

    private function initializeConsignmentSetting() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . $this->store_id . "' AND `group` = 'consignment_setting'");
        
        foreach ($query->rows as $result) {
            if (property_exists($this, $result['key'])) {
                $this->{$result['key']} = $result['value'];
            }
        }
    }

    private function initializeConsignmentNumber() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . $this->store_id . "' AND `group` = 'consignment_no' LIMIT 1");

        $result = $query->row;

        if (property_exists($this, $result['key'])) {
            $this->{$result['key']} = $result['value'];
        }
    }
    
    public function setStoreId($store_id) {
        $this->store_id = (int) $store_id;
    }

    public function getAppId() {
        return $this->app_id;
    }

    public function getAppKey() {
        return $this->app_key;
    }

    public function getConsignmentPrefix() {
        return $this->consignment_prefix;
    }

    public function getConsignmentNumber() {
        return $this->consignment_prefix . str_pad($this->con_no, 5, '0', STR_PAD_LEFT);
    }

    public function updateConsignmentNumber() {
        $this->con_no += 1;

        $this->db->query("UPDATE " . DB_PREFIX . "setting SET value = '" .$this->con_no. "' WHERE store_id = '" . $this->store_id . "' AND `group` = 'consignment_no' LIMIT 1");
    }

    public function callAPI($path, $data = array(), $method = 'GET') {
        $request_headers = [
            'Content-Type: application/json; charset=UTF-8',
            'app_id: ' . $this->getAppId(),
            'app_key: ' . $this->getAppKey(),
        ];

        $request_methods = [
            'HEAD',
            'GET',
            'POST',
            'PUT',
            'DELETE',
        ];

        $method = in_array($method, $request_methods) ? $method : 'GET';

		$defaults = array(
			CURLOPT_CUSTOMREQUEST   => $method,
			CURLOPT_HEADER          => 0,
            CURLOPT_HTTPHEADER      => $request_headers,
			CURLOPT_URL             => $this->api_server . $path,
			CURLOPT_USERAGENT       => 'Shipment Info for ABX Consignment',
			CURLOPT_RETURNTRANSFER  => 1,
			CURLOPT_TIMEOUT         => 30,
			CURLOPT_SSL_VERIFYPEER  => 0,
			CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_POSTFIELDS      => json_encode($data),
            CURLINFO_HEADER_OUT     => 0,
		);
		$ch = curl_init();

		curl_setopt_array($ch, $defaults);

        $response = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $data = json_decode($response, true);

        if (json_last_error()) {
            $data = [];
        }

        curl_close($ch);

        if ($response_code >= 200 && $response_code < 300) {
            return [
                'success' => true,
                'data' => $data,
            ];
        }

		return [
            'error' => true,
            'data' => $data,
        ];
    }

    public function saveShippingRequisitionInfo($data) {
        try {
            $count = $this->db->query("SELECT COUNT(*) as 'count' FROM `" . DB_PREFIX . "shipment_requisition` WHERE `con_no` = '" . $this->db->escape($data['con_no']) . "' LIMIT 1");

            if ($count->row['count'] == 1) {
                $result = $this->db->query("UPDATE " . DB_PREFIX . "shipment_requisition SET `con_no` = '" . $this->db->escape($data['con_no']) . "', `status_code` = '" . $this->db->escape($data['status_code']) . "', `status_desc` = '" . $this->db->escape($data['status_desc']) . "' WHERE `con_no` = '" . $this->db->escape($data['con_no']) . "' LIMIT 1");
            } else {
                $result = $this->db->query("INSERT INTO " . DB_PREFIX . "shipment_requisition SET `con_no` = '" . $this->db->escape($data['con_no']) . "', `status_code` = '" . $this->db->escape($data['status_code']) . "', `status_desc` = '" . $this->db->escape($data['status_desc']) . "'");
            }

            return $result;
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }
}