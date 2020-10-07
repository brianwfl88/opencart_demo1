<?php
class ControllerConsignmentSettings extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('consignment/settings');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title')); 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('consignment_setting', $this->request->post);		

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('consignment/settings', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getError();
		$this->getForm();

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('consignment/settings', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['text_app_id'] = $this->language->get('text_app_id');
		$this->data['text_app_key'] = $this->language->get('text_app_key');
		$this->data['text_consignment_prefix'] = $this->language->get('text_consignment_prefix');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_action'] = $this->language->get('column_action');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}
		
		$this->data['action'] = $this->url->link('consignment/settings', 'token=' . $this->session->data['token'], 'SSL');

		$this->template = 'consignment/settings.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$consignment_setting = $this->model_setting_setting->getSetting('consignment_setting');

		if (isset($this->request->post['app_id'])) {
			$this->data['app_id'] = $this->request->post['app_id'];
		} elseif (isset($consignment_setting['app_id'])) {
			$this->data['app_id'] = $consignment_setting['app_id'];
		} else {
			$this->data['app_id'] = '';
		}

		if (isset($this->request->post['app_key'])) {
			$this->data['app_key'] = $this->request->post['app_key'];
		} elseif (isset($consignment_setting['app_key'])) {
			$this->data['app_key'] = $consignment_setting['app_key'];
		} else {
			$this->data['app_key'] = '';
		}

		if (isset($this->request->post['consignment_prefix'])) {
			$this->data['consignment_prefix'] = $this->request->post['consignment_prefix'];
		} elseif (isset($consignment_setting['consignment_prefix'])) {
			$this->data['consignment_prefix'] = $consignment_setting['consignment_prefix'];
		} else {
			$this->data['consignment_prefix'] = '';
		}
	}

	protected function getError() {
		if (isset($this->error['app_id'])) {
			$this->data['error_app_id'] = $this->error['app_id'];
		} else {
			$this->data['error_app_id'] = '';
		}

		if (isset($this->error['app_key'])) {
			$this->data['error_app_key'] = $this->error['app_key'];
		} else {
			$this->data['error_app_key'] = '';
		}

		if (isset($this->error['consignment_prefix'])) {
			$this->data['error_consignment_prefix'] = $this->error['consignment_prefix'];
		} else {
			$this->data['error_consignment_prefix'] = '';
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'consignment/settings')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['app_id'])){
			$this->error['app_id'] = $this->language->get('error_app_id_empty');
		}
		else if (preg_match('/[^a-z0-9_-]+/i', $this->request->post['app_id'])){
			$this->error['app_id'] = $this->language->get('error_app_id_char');
		}

		if (empty($this->request->post['app_key'])){
			$this->error['app_key'] = $this->language->get('error_app_key_empty');
		}
		else if (preg_match('/[^a-z0-9_-]+/i', $this->request->post['app_key'])){
			$this->error['app_key'] = $this->language->get('error_app_key_char');
		}

		if (empty($this->request->post['consignment_prefix'])){
			$this->error['consignment_prefix'] = $this->language->get('error_consignment_prefix_empty');
		}
		else if (preg_match('/[^a-z0-9_-]+/i', $this->request->post['consignment_prefix'])){
			$this->error['consignment_prefix'] = $this->language->get('error_consignment_prefix_char');
		}
		else if (strlen($this->request->post['consignment_prefix']) < 5){
			$this->error['consignment_prefix'] = str_replace(':length:', 5, $this->language->get('error_consignment_prefix_length'));
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}       
	}
}
?>