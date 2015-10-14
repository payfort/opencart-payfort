<?php

class ControllerPaymentPayfortFort extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('payment/payfort_fort');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payfort_fort', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_sha1'] = $this->language->get('text_sha1');
        $this->data['text_sha256'] = $this->language->get('text_sha256');
        $this->data['text_sha512'] = $this->language->get('text_sha512');
        $this->data['text_en'] = $this->language->get('text_en');
        $this->data['text_ar'] = $this->language->get('text_ar');
        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_authorization'] = $this->language->get('text_authorization');
        $this->data['text_purchase'] = $this->language->get('text_purchase');
        $this->data['entry_hash_algorithm'] = $this->language->get('entry_hash_algorithm');

        $this->data['entry_merchant_identifier'] = $this->language->get('entry_merchant_identifier');
        $this->data['entry_access_code'] = $this->language->get('entry_access_code');
        $this->data['entry_request_sha_phrase'] = $this->language->get('entry_request_sha_phrase');
        $this->data['entry_response_sha_phrase'] = $this->language->get('entry_response_sha_phrase');

        $this->data['entry_sandbox'] = $this->language->get('entry_sandbox');
        $this->data['entry_language'] = $this->language->get('entry_language');
        $this->data['entry_command'] = $this->language->get('entry_command');
        $this->data['entry_total'] = $this->language->get('entry_total');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['payfort_fort_entry_merchant_identifier'])) {
            $this->data['error_payfort_fort_entry_merchant_identifier'] = $this->error['payfort_fort_entry_merchant_identifier'];
        } else {
            $this->data['error_payfort_fort_entry_merchant_identifier'] = '';
        }
 
        if (isset($this->error['payfort_fort_entry_access_code'])) {
            $this->data['error_payfort_fort_entry_access_code'] = $this->error['payfort_fort_entry_access_code'];
        } else {
            $this->data['error_payfort_fort_entry_access_code'] = '';
        }
        
        if (isset($this->error['payfort_fort_entry_request_sha_phrase'])) {
            $this->data['error_payfort_fort_entry_request_sha_phrase'] = $this->error['payfort_fort_entry_request_sha_phrase'];
        } else {
            $this->data['error_payfort_fort_entry_request_sha_phrase'] = '';
        }
        
        if (isset($this->error['payfort_fort_entry_response_sha_phrase'])) {
            $this->data['error_payfort_fort_entry_response_sha_phrase'] = $this->error['payfort_fort_entry_response_sha_phrase'];
        } else {
            $this->data['error_payfort_fort_entry_response_sha_phrase'] = '';
        }
 
 

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/payfort_fort', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/payfort_fort', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['payfort_fort_entry_merchant_identifier'])) {
            $this->data['payfort_fort_entry_merchant_identifier'] = $this->request->post['payfort_fort_entry_merchant_identifier'];
        } else {
            $this->data['payfort_fort_entry_merchant_identifier'] = $this->config->get('payfort_fort_entry_merchant_identifier');
        }
        
        if (isset($this->request->post['payfort_fort_entry_access_code'])) {
            $this->data['payfort_fort_entry_access_code'] = $this->request->post['payfort_fort_entry_access_code'];
        } else {
            $this->data['payfort_fort_entry_access_code'] = $this->config->get('payfort_fort_entry_access_code');
        }
        
        if (isset($this->request->post['payfort_fort_entry_command'])) {
            $this->data['payfort_fort_entry_command'] = $this->request->post['payfort_fort_entry_command'];
        } else {
            $this->data['payfort_fort_entry_command'] = $this->config->get('payfort_fort_entry_command');
        }
        
        if (isset($this->request->post['payfort_fort_entry_sandbox_mode'])) {
            $this->data['payfort_fort_entry_sandbox_mode'] = $this->request->post['payfort_fort_entry_sandbox_mode'];
        } else {
            $this->data['payfort_fort_entry_sandbox_mode'] = $this->config->get('payfort_fort_entry_sandbox_mode');
        }
        
        if (isset($this->request->post['payfort_fort_entry_request_sha_phrase'])) {
            $this->data['payfort_fort_entry_request_sha_phrase'] = $this->request->post['payfort_fort_entry_request_sha_phrase'];
        } else {
            $this->data['payfort_fort_entry_request_sha_phrase'] = $this->config->get('payfort_fort_entry_request_sha_phrase');
        }
        
        if (isset($this->request->post['payfort_fort_entry_response_sha_phrase'])) {
            $this->data['payfort_fort_entry_response_sha_phrase'] = $this->request->post['payfort_fort_entry_response_sha_phrase'];
        } else {
            $this->data['payfort_fort_entry_response_sha_phrase'] = $this->config->get('payfort_fort_entry_response_sha_phrase');
        }
        
        if (isset($this->request->post['payfort_fort_entry_language'])) {
            $this->data['payfort_fort_entry_language'] = $this->request->post['payfort_fort_entry_language'];
        } else {
            $this->data['payfort_fort_entry_language'] = $this->config->get('payfort_fort_entry_language');
        }
        
        if (isset($this->request->post['payfort_fort_entry_hash_algorithm'])) {
            $this->data['payfort_fort_entry_hash_algorithm'] = $this->request->post['payfort_fort_entry_hash_algorithm'];
        } else {
            $this->data['payfort_fort_entry_hash_algorithm'] = $this->config->get('payfort_fort_entry_hash_algorithm');
        }

        if (isset($this->request->post['payfort_fort_order_status_id'])) {
            $this->data['payfort_fort_order_status_id'] = $this->request->post['payfort_fort_order_status_id'];
        } else {
            $this->data['payfort_fort_order_status_id'] = $this->config->get('payfort_fort_order_status_id');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payfort_fort_geo_zone_id'])) {
            $this->data['payfort_fort_geo_zone_id'] = $this->request->post['payfort_fort_geo_zone_id'];
        } else {
            $this->data['payfort_fort_geo_zone_id'] = $this->config->get('payfort_fort_geo_zone_id');
        }

        if (isset($this->request->post['payfort_fort_status'])) {
            $this->data['payfort_fort_status'] = $this->request->post['payfort_fort_status'];
        } else {
            $this->data['payfort_fort_status'] = $this->config->get('payfort_fort_status');
        }

        if (isset($this->request->post['payfort_fort_sort_order'])) {
            $this->data['payfort_fort_sort_order'] = $this->request->post['payfort_fort_sort_order'];
        } else {
            $this->data['payfort_fort_sort_order'] = $this->config->get('payfort_fort_sort_order');
        }

        $this->template = 'payment/payfort_fort.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/payfort_fort')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payfort_fort_entry_merchant_identifier']) {
            $this->error['payfort_fort_entry_merchant_identifier'] = $this->language->get('error_payfort_fort_entry_merchant_identifier');
        }
        
        if (!$this->request->post['payfort_fort_entry_access_code']) {
            $this->error['payfort_fort_entry_access_code'] = $this->language->get('error_payfort_fort_entry_access_code');
        }
        
        if (!$this->request->post['payfort_fort_entry_request_sha_phrase']) {
            $this->error['payfort_fort_entry_request_sha_phrase'] = $this->language->get('error_payfort_fort_entry_request_sha_phrase');
        }
        
        if (!$this->request->post['payfort_fort_entry_response_sha_phrase']) {
            $this->error['payfort_fort_entry_response_sha_phrase'] = $this->language->get('error_payfort_fort_entry_response_sha_phrase');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>