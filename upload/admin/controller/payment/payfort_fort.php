<?php

class ControllerPaymentPayfortFort extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('payment/payfort_fort');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('payfort_fort', $this->request->post);

            $installments_post = $this->fixPostData($this->request->post, 'installments');
            $this->model_setting_setting->editSetting('payfort_fort_installments', $installments_post);

            $sadad_post = $this->fixPostData($this->request->post, 'sadad');
            $this->model_setting_setting->editSetting('payfort_fort_sadad', $sadad_post);
            
            $qpay_post = $this->fixPostData($this->request->post, 'qpay');
            $this->model_setting_setting->editSetting('payfort_fort_qpay', $qpay_post);
            
            
            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_sha1'] = $this->language->get('text_sha1');
        $this->data['text_sha256'] = $this->language->get('text_sha256');
        $this->data['text_sha512'] = $this->language->get('text_sha512');
        $this->data['text_hmac256'] = $this->language->get('text_hmac256');
        $this->data['text_hmac512'] = $this->language->get('text_hmac512');
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
        $this->data['text_store_language'] = $this->language->get('text_store_language');

        $this->data['entry_sandbox'] = $this->language->get('entry_sandbox');
        $this->data['entry_language'] = $this->language->get('entry_language');
        $this->data['entry_command'] = $this->language->get('entry_command');
        $this->data['entry_total'] = $this->language->get('entry_total');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_installments'] = $this->language->get('entry_installments');
        $this->data['entry_sadad'] = $this->language->get('entry_sadad');
        $this->data['entry_naps'] = $this->language->get('entry_naps');
        $this->data['entry_credit_card'] = $this->language->get('entry_credit_card');
        $this->data['entry_cc_integration_type'] = $this->language->get('entry_cc_integration_type');
        $this->data['entry_installments_integration_type'] = $this->language->get('entry_installments_integration_type');
        $this->data['help_cc_integration_type'] = $this->language->get('help_cc_integration_type');
        $this->data['help_installments_integration_type'] = $this->language->get('help_installments_integration_type');
        $this->data['text_merchant_page'] = $this->language->get('text_merchant_page');
        $this->data['text_merchant_page2'] = $this->language->get('text_merchant_page2');
        $this->data['text_redirection'] = $this->language->get('text_redirection');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['entry_debug'] = $this->language->get('entry_debug');
        $this->data['help_debug'] = $this->language->get('help_debug');
        $this->data['entry_gateway_currency'] = $this->language->get('entry_gateway_currency');
        $this->data['text_base_currency'] = $this->language->get('text_base_currency');
        $this->data['text_front_currency'] = $this->language->get('text_front_currency');
        $this->data['help_gateway_currency'] = $this->language->get('help_gateway_currency');
        
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_credit_card'] = $this->language->get('tab_credit_card');
        $this->data['tab_installments'] = $this->language->get('tab_installments');
        $this->data['tab_sadad'] = $this->language->get('tab_sadad');
        $this->data['tab_naps'] = $this->language->get('tab_naps');
        
        $this->data['entry_order_placement'] = $this->language->get('entry_order_placement');
        $this->data['help_order_placement'] = $this->language->get('help_order_placement');
        $this->data['text_on_success'] = $this->language->get('text_on_success');
        $this->data['text_always'] = $this->language->get('text_always');
        
        $url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG);
        $host_to_host_url = $url->link('payment/payfort_fort/response', '', 'SSL');
        $this->data['host_to_host_url'] = $host_to_host_url;
        
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
        
        if (isset($this->error['payfort_fort_payment_method_required'])) {
            $this->data['payfort_fort_payment_method_required'] = $this->error['payfort_fort_payment_method_required'];
        } else {
            $this->data['payfort_fort_payment_method_required'] = '';
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
        
        if (isset($this->request->post['payfort_fort_entry_gateway_currency'])) {
            $this->data['payfort_fort_entry_gateway_currency'] = $this->request->post['payfort_fort_entry_gateway_currency'];
        } else {
            $this->data['payfort_fort_entry_gateway_currency'] = $this->config->get('payfort_fort_entry_gateway_currency');
        }
        
        if (isset($this->request->post['payfort_fort_debug'])) {
            $this->data['payfort_fort_debug'] = $this->request->post['payfort_fort_debug'];
        } else {
            $this->data['payfort_fort_debug'] = $this->config->get('payfort_fort_debug');
        }
        
        if (isset($this->request->post['payfort_fort_order_placement'])) {
            $this->data['payfort_fort_order_placement'] = $this->request->post['payfort_fort_order_placement'];
        } else {
            $this->data['payfort_fort_order_placement'] = $this->config->get('payfort_fort_order_placement');
        }
        
        if (isset($this->request->post['payfort_fort_installments'])) {
            $this->data['payfort_fort_installments'] = $this->request->post['payfort_fort_installments'];
        } else {
            $this->data['payfort_fort_installments'] = $this->config->get('payfort_fort_installments');
        }
        
        if (isset($this->request->post['payfort_fort_sadad'])) {
            $this->data['payfort_fort_sadad'] = $this->request->post['payfort_fort_sadad'];
        } else {
            $this->data['payfort_fort_sadad'] = $this->config->get('payfort_fort_sadad');
        }
        
        if (isset($this->request->post['payfort_fort_naps'])) {
            $this->data['payfort_fort_naps'] = $this->request->post['payfort_fort_naps'];
        } else {
            $this->data['payfort_fort_naps'] = $this->config->get('payfort_fort_naps');
        }
        
        if (isset($this->request->post['payfort_fort_credit_card'])) {
            $this->data['payfort_fort_credit_card'] = $this->request->post['payfort_fort_credit_card'];
        } else {
            $this->data['payfort_fort_credit_card'] = $this->config->get('payfort_fort_credit_card');
        }

        if (isset($this->request->post['payfort_fort_cc_integration_type'])) {
            $this->data['payfort_fort_cc_integration_type'] = $this->request->post['payfort_fort_cc_integration_type'];
        } else {
            $this->data['payfort_fort_cc_integration_type'] = $this->config->get('payfort_fort_cc_integration_type');
        }

        if (isset($this->request->post['payfort_fort_installments_integration_type'])) {
            $this->data['payfort_fort_installments_integration_type'] = $this->request->post['payfort_fort_installments_integration_type'];
        } else {
            $this->data['payfort_fort_installments_integration_type'] = $this->config->get('payfort_fort_installments_integration_type');
        }
        
        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

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

        if (isset($this->request->post['payfort_fort_installments_sort_order'])) {
            $this->data['payfort_fort_installments_sort_order'] = $this->request->post['payfort_fort_installments_sort_order'];
        } else {
            $this->data['payfort_fort_installments_sort_order'] = $this->config->get('payfort_fort_installments_sort_order');
        }

        if (isset($this->request->post['payfort_fort_sadad_sort_order'])) {
            $this->data['payfort_fort_sadad_sort_order'] = $this->request->post['payfort_fort_sadad_sort_order'];
        } else {
            $this->data['payfort_fort_sadad_sort_order'] = $this->config->get('payfort_fort_sadad_sort_order');
        }
        
        if (isset($this->request->post['payfort_fort_qpay_sort_order'])) {
            $this->data['payfort_fort_qpay_sort_order'] = $this->request->post['payfort_fort_qpay_sort_order'];
        } else {
            $this->data['payfort_fort_qpay_sort_order'] = $this->config->get('payfort_fort_qpay_sort_order');
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
        
        if (!$this->request->post['payfort_fort_credit_card'] && !$this->request->post['payfort_fort_installments'] && !$this->request->post['payfort_fort_sadad'] && $this->request->post['payfort_fort_status'] && !$this->request->post['payfort_fort_naps']) {
            $this->error['payfort_fort_payment_method_required'] = $this->language->get('payfort_fort_payment_method_required');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function install() {
            $this->load->model('setting/extension');
            $this->model_setting_extension->install('payment', 'payfort_fort_installments');
            $this->model_setting_extension->install('payment', 'payfort_fort_sadad');
            $this->model_setting_extension->install('payment', 'payfort_fort_qpay');
}

    public function uninstall() {
            $this->load->model('setting/extension');
            $this->model_setting_extension->uninstall('payment', 'payfort_fort_installments');
            $this->model_setting_extension->uninstall('payment', 'payfort_fort_sadad');
            $this->model_setting_extension->uninstall('payment', 'payfort_fort_qpay');
            
            $this->load->model('setting/setting');
            $this->model_setting_setting->deleteSetting('payfort_fort_installments');
            $this->model_setting_setting->deleteSetting('payfort_fort_sadad');
            $this->model_setting_setting->deleteSetting('payfort_fort_qpay');
    }

    private function fixPostData($post, $code) {
            $newPost = array();
            foreach($post as $key => $value) {
                $newstr = substr_replace($key, '_'.$code, strlen('payfort_fort'), 0);
                if(isset($this->request->post[$newstr])) {
                    $newPost[$newstr] = $this->request->post[$newstr]; 
                }
                else{
                    $newPost[$newstr] = $value; 
                }
            }
            return $newPost;
    }
}

?>
