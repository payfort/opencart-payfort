<?php 
class ModelPaymentPayfortFort extends Model {
	public function getMethod($address, $total) {
            $this->language->load('payment/payfort_fort');
            $sadad_enabled = $this->config->get('payfort_fort_sadad');
            $naps_enabled = $this->config->get('payfort_fort_naps');
            $credit_card_enabled = $this->config->get('payfort_fort_credit_card');
            $extra_options = '<script>';
            if ($sadad_enabled){
                $extra_options .= '$("label[for=payfort_fort]").closest("tr").after(\'<tr class="highlight"><td><input type="radio" name="payment_method" value="payfort_fort" id="payfort_sadad"></td><td><label for="payfort_sadad">'.$this->language->get('text_sadad').'</label></td></tr>\"\')';
            }
            if ($naps_enabled){
                $extra_options .= ';$("label[for=payfort_fort]").closest("tr").after(\'<tr class="highlight"><td><input type="radio" name="payment_method" value="payfort_fort" id="payfort_naps"></td><td><label for="payfort_naps">'.$this->language->get('text_naps').'</label></td></tr>\"\')';
            }
            if (!$credit_card_enabled){
                $extra_options .= ';$("#payfort_fort").closest("tr").remove()';
            }
            $extra_options .= '</script>';
			$method_data = array(
				'code'       => 'payfort_fort',
				'title'      => $this->language->get('text_title') . $extra_options,
				'sort_order' => $this->config->get('payfort_fort_sort_order')
			);

		return $method_data;
	}
}
?>