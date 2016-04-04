<?php

class ControllerPaymentPayfortFort extends Controller {

    private $_gatewayHost        = 'https://checkout.payfort.com/';
    private $_gatewaySandboxHost = 'https://sbcheckout.payfort.com/';
    //private $_gatewaySandboxHost = 'https://checkout.fortstg.com/';
    public function index() {
        $this->language->load('payment/payfort_fort');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['text_general_error']  = $this->language->get('text_general_error');
        $this->data['text_error_card_decline'] = $this->language->get('text_error_card_decline');
        
        $this->data['payfort_fort_cc_integration_type'] = $this->config->get('payfort_fort_cc_integration_type');
        
        $this->data['merchant_page_data'] = '';
        if($this->config->get('payfort_fort_cc_integration_type') == 'merchantPage') {
            $order_id = $this->session->data['order_id'];
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = 'Credit / Debit Card', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
            $this->data['merchant_page_data'] = $this->getMerchantPageData();
            //$this->model_checkout_order->addOrderHistory($order_id, 1, 'Pending Payment', false);
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payfort_fort.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payfort_fort.tpl';
        } else {
            $this->template = 'default/template/payment/payfort_fort.tpl';
        }
        $this->render();
    }
    
    public function response(){
        $fortParams = array_merge($_GET,$_POST); // never include PUT and other restful actions
        if (isset($fortParams['response_code']) && isset($fortParams['merchant_reference'])){
            $this->language->load('payment/payfort_fort');
            $this->load->model('checkout/order');
            $order_id = $fortParams['merchant_reference'];
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $success = false;
            $params = $fortParams;
            $hashString = '';
            $signature = $fortParams['signature'];
            
            unset($params['signature']);
            unset($params['route']);
            $trueSignature = $this->_calculateSignature($params, 'response');
            if ($trueSignature != $signature){
                $success = false;
                if ($this->config->get('payfort_fort_debug')) {
                    $log = new Log('payfort_fort.log');
                    $log->write(sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $trueSignature, $signature));
                }
            }
            else{
                $response_code      = $params['response_code'];
                $response_message   = $params['response_message'];
                $status             = $params['status'];
                
                if (substr($response_code, 2) != '000'){

                }
                else{
                    $success = true;
                    $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
                    $this->model_checkout_order->update($order_id, $this->config->get('payfort_fort_order_status_id'), 'Paid: ' . $order_id, false);
                    if($this->config->get('payfort_fort_cc_integration_type') == 'merchantPage') {
                        echo '<script>window.top.location.href = "'.$this->url->link('payment/payfort_fort/success').'"</script>';
                        exit;
                    }
                    else{
                        header('location:'.$this->url->link('payment/payfort_fort/success'));
                    }
                    
                }
            }
            
            if (!$success){
                //$this->model_checkout_order->confirm($order_id, 10, 'Payment Error', false);
                $this->session->data['error'] = $this->language->get('text_payment_failed').$params['response_message'];
                $this->model_checkout_order->update($order_id, 10, 'Payment Failed', false);
                if($this->config->get('payfort_fort_cc_integration_type') == 'merchantPage') {
                    echo '<script>window.top.location.href = "'.$this->url->link('checkout/checkout').'"</script>';
                    exit;
                }
                else{
                    header('location:'.$this->url->link('payment/payfort_fort/error'));
                }
            }
            
        }
    }

    public function send() {

        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $postData = array(
            'amount'                => round($order_info['total'] * $order_info['currency_value'],2) * 100,
            'currency'              => strtoupper($order_info['currency_code']),
            'merchant_identifier'   => $this->config->get('payfort_fort_entry_merchant_identifier'),
            'access_code'           => $this->config->get('payfort_fort_entry_access_code'),
            'merchant_reference'    => $order_id,
            'customer_email'        => $order_info['email'],
            'command'               => $this->config->get('payfort_fort_entry_command'),
            'language'              => $this->config->get('payfort_fort_entry_language'),
            'return_url'            => $this->url->link('payment/payfort_fort/response', '', 'SSL'),
        );
        
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = 'Credit / Debit Card', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

        //calculate request signature
        $signature = $this->_calculateSignature($postData, 'request');
        $postData['signature'] = $signature;
        
        if ($this->config->get('payfort_fort_entry_sandbox_mode')){
            $gatewayUrl = $this->_gatewaySandboxHost.'FortAPI/paymentPage';
        }
        else{
            $gatewayUrl = $this->_gatewayHost.'FortAPI/paymentPage';
        }
        
        $form =  '<form style="display:none" name="payfortpaymentform" id="payfortpaymentform" method="post" action="'.$gatewayUrl.'" id="form1" name="form1">';
        
        foreach ($postData as $k => $v){
            $form .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
        }
        
        $form .= '<input type="submit" value="" id="submit" name="submit2">';
        
        $json = array();
        
        $json['form'] = $form;
        
        //$this->model_checkout_order->confirm($order_id, 1, 'Pending Payment', false);

        $this->response->setOutput(json_encode($json));

    }
    
    public function merchantPageResponse(){
        $fortParams = array_merge($_GET,$_POST); //never use $_REQUEST, it might include PUT .. etc
        
        if ($this->config->get('payfort_fort_debug')) {
            $log = new Log('payfort_fort.log');
            $log->write(print_r($fortParams, 1));
        }
        
        if (isset($fortParams['response_code']) && isset($fortParams['merchant_reference'])){
            $this->language->load('payment/payfort_fort');
            $this->load->model('checkout/order');
            $order_id = $fortParams['merchant_reference'];
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $success = false;
            $params = $fortParams;
            $signature = $fortParams['signature'];
            
            unset($params['signature']);
            unset($params['route']);
            $trueSignature = $this->_calculateSignature($params, 'response');
            if ($trueSignature != $signature){
                $success = false;
                if ($this->config->get('payfort_fort_debug')) {
                    $log = new Log('payfort_fort.log');
                    $log->write(sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $trueSignature, $signature));
                }
            }
            else{
                $response_code      = $params['response_code'];
                $response_message   = $params['response_message'];
                $status             = $params['status'];
                
                if (substr($response_code, 2) != '000'){
                    $success = false;
                }
                else{
                    $success = true;
                    $host2HostParams = $this->merchantPageNotifyFort($fortParams);
                    if ($this->config->get('payfort_fort_debug')) {
                        $log = new Log('payfort_fort.log');
                        $log->write(print_r($host2HostParams, 1));
                    }
                    if(!$host2HostParams) {
                        $success = false;
                        if ($this->config->get('payfort_fort_debug')) {
                            $log = new Log('payfort_fort.log');
                            $log->write('Invalid response parameters.');
                        }
                    }
                    else {
                        $params = $host2HostParams;
                        $signature = $host2HostParams['signature'];

                        unset($params['signature']);
                        unset($params['route']);
                        $trueSignature = $this->_calculateSignature($params, 'response');
                        if ($trueSignature != $signature){
                            $success = false;
                            if ($this->config->get('payfort_fort_debug')) {
                                $log = new Log('payfort_fort.log');
                                $log->write(sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $trueSignature, $signature));
                            }
                        }
                        else{
                            $response_code      = $params['response_code'];
                            
                            if($response_code == '20064' && isset($params['3ds_url'])) {
                                //redirect to 3ds page
                                $success = true;
                                header('location:'.$params['3ds_url']);
                                exit;
                            }
                            else{
                                if (substr($response_code, 2) != '000'){
                                    $success = false;
                                }
                                if($success) {
                                    $success = true;
                                    $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
                                    $this->model_checkout_order->update($order_id, $this->config->get('payfort_fort_order_status_id'), 'Paid: ' . $order_id, false);
                                    //$this->model_checkout_order->confirm($order_id, $this->config->get('payfort_fort_order_status_id'), 'Paid: ' . $order_id, false);
                                    echo '<script>window.top.location.href = "'.$this->url->link('payment/payfort_fort/success').'"</script>';
                                    exit;
                                }
                            }
                        }
                    }
                }
            }
            
            if (!$success){
                $this->model_checkout_order->confirm($order_id, 10, 'Payment Failed', false);
                //$this->model_checkout_order->update($order_id, 10, 'Payment Failed', false);
                $this->session->data['error'] = $this->language->get('text_payment_failed').$params['response_message'];
                echo '<script>window.top.location.href = "'.$this->url->link('checkout/checkout').'"</script>';
                exit;
            }
        }
    }
    
    private function merchantPageNotifyFort($fortParams) {
        //send host to host
        
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
        $postData = array(
            'merchant_reference'    => $fortParams['merchant_reference'],
            'access_code'           => $this->config->get('payfort_fort_entry_access_code'),
            'command'               => $this->config->get('payfort_fort_entry_command'),
            'merchant_identifier'   => $this->config->get('payfort_fort_entry_merchant_identifier'),
            'customer_ip'           => $this->request->server['REMOTE_ADDR'],
            'amount'                => $this->_convertFortAmount($order_info['total'], $order_info['currency_value'], $order_info['currency_code']),
            'currency'              => strtoupper($order_info['currency_code']),
            'customer_email'        => $order_info['email'],
            'customer_name'         => trim($order_info['payment_firstname'].' '.$order_info['payment_lastname']),
            'token_name'            => $fortParams['token_name'],
            'language'              => $this->config->get('payfort_fort_entry_language'),
            'return_url'            => $this->url->link('payment/payfort_fort/response', '', 'SSL'),
        );
        //calculate request signature
        $signature = $this->_calculateSignature($postData, 'request');
        $postData['signature'] = $signature;
        
        if ($this->config->get('payfort_fort_debug')) {
            $log = new Log('payfort_fort.log');
            $log->write(print_r($postData, 1));
        }
        
        if ($this->config->get('payfort_fort_entry_sandbox_mode')){
            $gatewayUrl = $this->_gatewaySandboxHost.'FortAPI/paymentApi';
        }
        else{
            $gatewayUrl = $this->_gatewayHost.'FortAPI/paymentApi';
        }
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0";
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
                //'Accept: application/json, application/*+json',
                //'Connection:keep-alive'
        ));
        curl_setopt($ch, CURLOPT_URL, $gatewayUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "compress, gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects		
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); // The number of seconds to wait while trying to connect
        //curl_setopt($ch, CURLOPT_TIMEOUT, Yii::app()->params['apiCallTimeout']); // timeout in seconds
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        
        $response_data = array();

        //parse_str($response, $response_data);
        curl_close($ch);
            
        
        $array_result    = json_decode($response, true);
        
        if ($this->config->get('payfort_fort_debug')) {
            $log = new Log('payfort_fort.log');
            $log->write(print_r($array_result, 1));
        }
        
        if(!$response || empty($array_result)) {
            return false;
        }
        return $array_result;
    }
    
    
    
    private function getMerchantPageData() {
            $this->language->load('payment/payfort_fort');
            $this->load->model('checkout/order');
            //$this->load->model('payment/pp_pro_iframe');
            $order_id = $this->session->data['order_id'];
            
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            
            $iframe_params = array(
                'merchant_identifier'   => $this->config->get('payfort_fort_entry_merchant_identifier'),
                'access_code'           => $this->config->get('payfort_fort_entry_access_code'),
                'merchant_reference'    => $order_id,
                'service_command'       => 'TOKENIZATION',
                'language'              => $this->config->get('payfort_fort_entry_language'),
                'return_url'            => $this->url->link('payment/payfort_fort/merchantPageResponse', '', 'SSL'),
            );

            //calculate request signature
            $signature = $this->_calculateSignature($iframe_params, 'request');
            $iframe_params['signature'] = $signature;
            
            if ($this->config->get('payfort_fort_entry_sandbox_mode')) {
                    $fort_url = $this->_gatewaySandboxHost.'FortAPI/paymentPage';
            } else {
                    $fort_url = $this->_gatewayHost.'FortAPI/paymentPage';
            }
            
            $merchant_page_url = $fort_url;
            
            return array('url' => $fort_url, 'params' => $iframe_params);
    }
    
    public function merchantPageCancel() {
        $this->language->load('payment/payfort_fort');
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        //$order_info = $this->model_checkout_order->getOrder($order_id);
        //$this->model_checkout_order->addOrderHistory($order_id, 7, 'Payment Canceled', false);
        //$this->model_checkout_order->confirm($order_id, 7, 'Payment Canceled', false);
        //$this->model_checkout_order->update($order_id, 7, 'Payment Canceled', false);
        $this->session->data['error'] = $this->language->get('text_payment_canceled');
        header('location:'.$this->url->link('checkout/checkout'));
    }
    
    public function success() { 	
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}	
									   
        $this->language->load('payment/payfort_fort');
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->language->get('heading_success_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('payment/payfort_fort/success'),
        	'text'      => $this->language->get('text_p_success'),
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_success_title');
		
		if ($this->customer->isLogged()) {
    		$this->data['text_message'] = sprintf($this->language->get('text_success_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
    		$this->data['text_message'] = sprintf($this->language->get('text_success_guest'), $this->url->link('information/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);
				
		$this->response->setOutput($this->render());
  	}
    
    public function error() { 	
			   
		$this->language->load('payment/payfort_fort');
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->language->get('heading_failed_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('payment/payfort_fort/error'),
        	'text'      => $this->language->get('text_failed'),
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_failed_title');
		
		if ($this->customer->isLogged()) {
    		$this->data['text_message'] = sprintf($this->language->get('text_failed_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
    		$this->data['text_message'] = sprintf($this->language->get('text_failed_guest'), $this->url->link('information/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);
				
		$this->response->setOutput($this->render());
    }
    
    /**
     * calculate fort signature
     * @param array $arr_data
     * @param sting $sign_type request or response
     * @return string fort signature
     */
    private function _calculateSignature($arr_data, $sign_type = 'request') {

        $shaString = '';

        ksort($arr_data);
        foreach ($arr_data as $k=>$v){
            $shaString .= "$k=$v";
        }

        if($sign_type == 'request') {
            $shaString = $this->config->get('payfort_fort_entry_request_sha_phrase') . $shaString . $this->config->get('payfort_fort_entry_request_sha_phrase');
        }
        else{
            $shaString = $this->config->get('payfort_fort_entry_response_sha_phrase') . $shaString . $this->config->get('payfort_fort_entry_response_sha_phrase');
        }
        $signature = hash($this->config->get('payfort_fort_entry_hash_algorithm') ,$shaString);

        return $signature;
    }

    /**
     * Convert Amount with dicemal points
     * @param decimal $amount
     * @param decimal $currency_value
     * @param string  $currency_code
     * @return decimal
     */
    private function _convertFortAmount($amount, $currency_value, $currency_code) {
        $new_amount = 0;
        //$decimal_points = $this->currency->getDecimalPlace();
        $decimal_points = $this->getCurrencyDecimalPoints($currency_code);
        $new_amount = round($amount * $currency_value, $decimal_points) * (pow(10, $decimal_points));
        return $new_amount;
    }
    
    /**
     * 
     * @param string $currency
     * @param integer 
     */
    private function getCurrencyDecimalPoints($currency) {
        $decimalPoint  = 2;
        $arrCurrencies = array(
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        );
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }
        return $decimalPoint;
    }
}

