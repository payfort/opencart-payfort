<?php

require_once DIR_SYSTEM . '/library/payfortFort/init.php';

class ModelPaymentPayfortFortInstallments extends Model
{

    public $pfConfig;
    public $pfHelper;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->pfConfig = Payfort_Fort_Config::getInstance();
        $this->pfHelper = Payfort_Fort_Helper::getInstance();
    }

    public function getMethod($address, $total)
    {
        $this->language->load('payment/payfort_fort');
        $enabled = $this->pfConfig->isInstallmentsActive();

        $status = true;

        if (!$enabled) {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => PAYFORT_FORT_PAYMENT_METHOD_INSTALLMENTS,
                'title'      => $this->language->get('text_installments'),
                'sort_order' => $this->config->get('payfort_fort_installments_sort_order')
            );
        }

        return $method_data;
    }

}

?>