<div id="payfort_fort_msg" class="warning" style="display:none"></div>
<form id="frm_payfort_fort_payment" class="payfort-fort-confirmation-form form-horizontal" method="POST" action="<?php echo $payment_request_params['url']; ?>">
    <?php foreach ($payment_request_params['params'] as $k => $v): ?>
        <input type="hidden" name="<?php echo $k?>" value="<?php echo $v?>">
    <?php endforeach; ?>
        <div class="content" id="payment">
            <table class="form">
              <tr>
                <td><?php echo $text_card_holder_name; ?></td>
                <td><input type="text" id="payfort_fort_card_holder_name" name="card_holder_name" value="" autocomplete="off" maxlength="50" /></td>
              </tr>
              <tr>
                <td><?php echo $text_card_number; ?></td>
                <td><input type="text" id="payfort_fort_card_number" name="card_number" value="" autocomplete="off" maxlength="16" /></td>
              </tr>
              <tr>
                <td><?php echo $text_expiry_date; ?></td>
                <td><select id="payfort_fort_expiry_month">
                    <?php foreach ($months as $month) { ?>
                    <option value="<?php echo $month['value']; ?>"><?php echo $month['value'].' - '.$month['text']; ?></option>
                    <?php } ?>
                  </select>
                  /
                  <select id="payfort_fort_expiry_year">
                    <?php foreach ($year_expire as $year) { ?>
                    <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                    <?php } ?>
                  </select>
                  <input type="hidden" id="payfort_fort_expiry" name="expiry_date"/>
                </td>
              </tr>
              <tr>
                <td><?php echo $text_cvc_code; ?></td>
                <td><input type="text" id="payfort_fort_card_security_code" name="card_security_code" value="" size="3" maxlength="4" autocomplete="off" /><br> <?php echo $help_cvc_code;?></td>
              </tr>
            </table>
        </div>
</form>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
    var arr_messages = [];
    <?php echo "$arr_js_messages";?>
 //--></script>   
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function () {
    var isValid = payfortFortMerchantPage2.validateCcForm();
    if(isValid) {
        $('#frm_payfort_fort_payment').submit();
    }
});
//--></script>

<script type="text/javascript" src="catalog/view/javascript/payfort_fort/jquery.creditCardValidator.js"/>
<script type="text/javascript" src="catalog/view/javascript/payfort_fort/payfort_fort.js"/>