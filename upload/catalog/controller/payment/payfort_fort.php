<?php

require_once DIR_SYSTEM . '/library/payfortFort/init.php';

class ControllerPaymentPayfortFort extends Controller
{

    public $paymentMethod;
    public $integrationType;
    public $pfConfig;
    public $pfPayment;
    public $pfHelper;
    public $pfOrder;
    public $madaBranding;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->pfConfig        = Payfort_Fort_Config::getInstance();
        $this->pfPayment       = Payfort_Fort_Payment::getInstance();
        $this->pfHelper        = Payfort_Fort_Helper::getInstance();
        $this->pfOrder         = new Payfort_Fort_Order();
        $this->integrationType = $this->pfConfig->getCcIntegrationType();
        $this->madaBranding    = $this->pfConfig->getCcMadaBranding();      
        $this->paymentMethod   = PAYFORT_FORT_PAYMENT_METHOD_CC;
    }

    public function index()
    {
        $this->language->load('payment/payfort_fort');
        $this->data['button_confirm']          = $this->language->get('button_confirm');
        $this->data['text_general_error']      = $this->language->get('text_general_error');
        $this->data['text_error_card_decline'] = $this->language->get('text_error_card_decline');

        $this->data['payfort_fort_cc_integration_type'] = $this->integrationType;
        $frontCurrency = $this->pfHelper->getFrontCurrency();
        $baseCurrency  = $this->pfHelper->getBaseCurrency();        
        $currency      = $this->pfHelper->getFortCurrency($baseCurrency, $frontCurrency);
        if ($currency == 'SAR') {
            $this->data['payfort_fort_cc_mada_branding']    = $this->madaBranding;
        }
        else 
        {
            $this->data['payfort_fort_cc_mada_branding']    = 'Disabled';            
        }
        $this->load->model('payment/payfort_fort');
        $this->data['payment_request_params'] = '';
        $template = 'payfort_fort.tpl';
        if ($this->pfConfig->isCcMerchantPage()) {
            $template                             = 'payfort_fort_merchant_page.tpl';
            $this->data['payment_request_params'] = $this->pfPayment->getPaymentRequestParams($this->paymentMethod, $this->integrationType);
            //$this->model_checkout_order->addOrderHistory($order_id, 1, 'Pending Payment', false);
        }
        elseif ($this->pfConfig->isCcMerchantPage2()) {
            $template                             = 'payfort_fort_merchant_page2.tpl';
            $this->data['payment_request_params'] = $this->pfPayment->getPaymentRequestParams($this->paymentMethod, $this->integrationType);
            
            $this->data['text_card_holder_name'] = $this->language->get('text_card_holder_name');
            $this->data['text_card_number'] = $this->language->get('text_card_number');
            $this->data['text_expiry_date'] = $this->language->get('text_expiry_date');
            $this->data['text_cvc_code'] = $this->language->get('text_cvc_code');
            $this->data['help_cvc_code'] = $this->language->get('help_cvc_code');
            
            $arr_js_messages = 
                    array(
                        'error_invalid_card_number' => $this->language->get('error_invalid_card_number'),
                        'error_invalid_card_holder_name' => $this->language->get('error_invalid_card_holder_name'),
                        'error_invalid_expiry_date' => $this->language->get('error_invalid_expiry_date'),
                        'error_invalid_cvc_code' => $this->language->get('error_invalid_cvc_code'),
                        'error_invalid_cc_details' => $this->language->get('error_invalid_cc_details'),
                    );
                    
            $this->data['arr_js_messages'] = $this->pfHelper->loadJsMessages($arr_js_messages);
            $this->data['months'] = array();

            for ($i = 1; $i <= 12; $i++) {
                    $this->data['months'][] = array(
                            'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
                            'value' => sprintf('%02d', $i)
                    );
            }

            $today = getdate();

            $this->data['year_expire'] = array();

            for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
                    $this->data['year_expire'][] = array(
                            'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                            'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)) 
                    );
            }
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template)) {
            $this->template = $this->config->get('config_template') . '/template/payment/' . $template;
        }
        else {
            $this->template = 'default/template/payment/' . $template;
        }
        $this->render();
    }

    public function send()
    {
        $form = $this->pfPayment->getPaymentRequestForm($this->paymentMethod);
        
        $json = array('form' => $form);
        $this->response->setOutput(json_encode($json));
    }

    public function response()
    {
        $this->_handleResponse('offline');
    }

    public function responseOnline()
    {

        $this->_handleResponse('online');
    }

    public function merchantPageResponse()
    {
        $this->_handleResponse('online', $this->integrationType);
    }

    private function _handleResponse($response_mode = 'online', $integration_type = PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION)
    {
        $response_params = array_merge($this->request->get, $this->request->post); //never use $_REQUEST, it might include PUT .. etc

        $success = $this->pfPayment->handleFortResponse($response_params, $response_mode, $integration_type);
        if ($success) {
            $redirectUrl = 'payment/payfort_fort/success';
        }
        else {
            $redirectUrl = 'checkout/checkout';
        }
        if ($this->pfConfig->isCcMerchantPage()) {
            echo '<script>window.top.location.href = "' . $this->url->link($redirectUrl) . '"</script>';
            exit;
        }
        else {
            header('location:' . $this->url->link($redirectUrl));
        }
    }

    public function merchantPageCancel()
    {
        $this->pfPayment->merchantPageCancel();
        header('location:' . $this->url->link('checkout/checkout'));
    }

    public function success()
    {
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
        }
        else {
            $this->data['text_message'] = sprintf($this->language->get('text_success_guest'), $this->url->link('information/contact'));
        }

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        }
        else {
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

    public function error()
    {

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
        }
        else {
            $this->data['text_message'] = sprintf($this->language->get('text_failed_guest'), $this->url->link('information/contact'));
        }

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        }
        else {
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

}
