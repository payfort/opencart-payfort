<?php

class ControllerPaymentPayfortFort extends Controller {

    public function index() {
        $this->language->load('payment/payfort_fort');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payfort_fort.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payfort_fort.tpl';
        } else {
            $this->template = 'default/template/payment/payfort_fort.tpl';
        }
        $this->render();
    }
    
    public function response(){

        if (isset($_GET['response_code']) && isset($_GET['merchant_reference'])){
            
            $this->load->model('checkout/order');
            $order_id = $this->session->data['order_id'];
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $success = false;
            $params = $_GET;
            $hashString = '';
            $signature = $_GET['signature'];
            
            unset($params['signature']);
            unset($params['route']);
            ksort($params);
            
            foreach ($params as $k=>$v){
                if ($v != ''){
                    $hashString .= strtolower($k).'='.$v;
                }
            }

            $hashString = $this->config->get('payfort_fort_entry_response_sha_phrase') . $hashString . $this->config->get('payfort_fort_entry_response_sha_phrase');
            $trueSignature = hash($this->config->get('payfort_fort_entry_hash_algorithm') ,$hashString);
            
            if ($trueSignature != $signature){
                $success = false;
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
                    header('location:'.$this->url->link('checkout/success'));
                }
            }
            
            if (!$success){
                //$this->model_checkout_order->confirm($order_id, 10, 'Payment Error', false);
                $this->model_checkout_order->update($order_id, 10, 'Payment Failed', false);
                header('location:'.$this->url->link('checkout/checkout'));
            }
            
        }
    }

    public function send() {

        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $postData = array(
            'amount'                => $order_info['total'] * 100,
            'currency'              => strtoupper($order_info['currency_code']),
            'merchant_identifier'   => $this->config->get('payfort_fort_entry_merchant_identifier'),
            'access_code'           => $this->config->get('payfort_fort_entry_access_code'),
            'merchant_reference'    => $order_id,
            'customer_email'        => $order_info['email'],
            'command'               => $this->config->get('payfort_fort_entry_command'),
            'language'              => $this->config->get('payfort_fort_entry_language'),
            'return_url'            => $this->url->link('payment/payfort_fort/response'),
        );
        
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = 'Credit / Debit Card', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        
        $isSADAD = isset($_POST['SADAD']) ? $_POST['SADAD'] : false;
        $isNaps = isset($_POST['NAPS']) ? $_POST['NAPS'] : false;

        if ($isSADAD == "true"){
            $postData['payment_option'] = 'SADAD';
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = 'SADAD', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        }
        else if ($isNaps == "true"){
            $postData['payment_option'] = 'NAPS';
            $postData['order_description'] = $order_id;
        }
        //calculate request signature
        $shaString = '';
        ksort($postData);
        foreach ($postData as $k=>$v){
            $shaString .= "$k=$v";
        }

        $shaString = $this->config->get('payfort_fort_entry_request_sha_phrase') . $shaString . $this->config->get('payfort_fort_entry_request_sha_phrase');
        $signature = hash($this->config->get('payfort_fort_entry_hash_algorithm') ,$shaString);
        
        if ($this->config->get('payfort_fort_entry_sandbox_mode')){
            $gatewayUrl = 'https://sbcheckout.payfort.com/FortAPI/paymentPage';
        }
        else{
            $gatewayUrl = 'https://checkout.payfort.com/FortAPI/paymentPage';
        }
        
        $form =  '<form style="display:none" name="payfortpaymentform" id="payfortpaymentform" method="post" action="'.$gatewayUrl.'" id="form1" name="form1">';
        
        foreach ($postData as $k => $v){
            $form .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
        }
        
        $form .= '<input type="hidden" name="signature" value="'.$signature.'">';
        $form .= '<input type="submit" value="" id="submit" name="submit2">';
        
        $json = array();
        
        $json['form'] = $form;
        
        $this->model_checkout_order->confirm($order_id, 1, 'Pending Payment', false);

        $this->response->setOutput(json_encode($json));

    }
}
