<?php
// this is a demo api call
class ControllerConsignmentShippingInfo extends Controller {
    protected $data = [];
    protected $consignment_api;

    public function __construct($registry) {
        parent::__construct($registry);

        $this->load->library('consignment_api');
        $this->consignment_api = new ConsignmentApi($this->registry);
    }
    
	public function index() {
        $this->load->library('consignment_api');
        
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->getFormData();

            $response = $this->consignment_api->callAPI('SmartEDI/shipment_info', $this->data, 'POST');

            if (!empty($response['success'])) {
                $result = [
                    'con_no' => $response['data']['res']['shipment']['con_no'],
                    'status_code' => $response['data']['res']['shipment']['status_code'],
                    'status_desc' => $response['data']['res']['shipment']['status_desc'],
                ];

                print_r($result);

                $result = $this->consignment_api->saveShippingRequisitionInfo($result);

                if ($result) {
                    $this->consignment_api->updateConsignmentNumber();
                }

                $this->response->setOutput(json_encode($response));
            }
        } else {
            echo '<h1>TEST ROUTE FOR API</h1>';
        }
    }

    public function getFormData() {
        $json_data = file_get_contents('php://input');

        $data = json_decode($json_data, true);
        
        $data['req']['shipment']['con_no'] = $this->consignment_api->getConsignmentNumber();

        $this->data = $data;
    }

    public function validate() {
        return true;
    }
}