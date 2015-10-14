<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_language; ?></td>
            <td><select name="payfort_fort_entry_language">
                <?php if ($payfort_fort_entry_language == 'en') { ?>
                <option value="en" selected="selected"><?php echo $text_en; ?></option>
                <?php } else { ?>
                <option value="en"><?php echo $text_en; ?></option>
                <?php } ?>
                <?php if ($payfort_fort_entry_language == 'ar') { ?>
                <option value="ar" selected="selected"><?php echo $text_ar; ?></option>
                <?php } else { ?>
                <option value="ar"><?php echo $text_ar; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant_identifier; ?></td>
            <td><input type="text" size ="50" name="payfort_fort_entry_merchant_identifier" value="<?php echo $payfort_fort_entry_merchant_identifier; ?>" />
              <?php if ($error_payfort_fort_entry_merchant_identifier) { ?>
              <span class="error"><?php echo $error_payfort_fort_entry_merchant_identifier; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_access_code; ?></td>
            <td><input type="text" size ="50" name="payfort_fort_entry_access_code" value="<?php echo $payfort_fort_entry_access_code; ?>" />
              <?php if ($error_payfort_fort_entry_access_code) { ?>
              <span class="error"><?php echo $error_payfort_fort_entry_access_code; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_command; ?></td>
            <td><select name="payfort_fort_entry_command">
                <?php if ($payfort_fort_entry_command == 'PURCHASE') { ?>
                <option value="PURCHASE" selected="selected"><?php echo $text_purchase; ?></option>
                <?php } else { ?>
                <option value="PURCHASE"><?php echo $text_purchase; ?></option>
                <?php } ?>
                <?php if ($payfort_fort_entry_command == 'AUTHORIZATION') { ?>
                <option value="AUTHORIZATION" selected="selected"><?php echo $text_authorization; ?></option>
                <?php } else { ?>
                <option value="AUTHORIZATION"><?php echo $text_authorization; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        <tr>
            <td><?php echo $entry_hash_algorithm; ?></td>
            <td><select name="payfort_fort_entry_hash_algorithm">
                <?php if ($payfort_fort_entry_hash_algorithm == 'sha1') { ?>
                <option value="sha1" selected="selected"><?php echo $text_sha1; ?></option>
                <?php } else { ?>
                <option value="sha1"><?php echo $text_sha1 ?></option>
                <?php } ?>
                <?php if ($payfort_fort_entry_hash_algorithm == 'sha256') { ?>
                <option value="sha256" selected="selected"><?php echo $text_sha256; ?></option>
                <?php } else { ?>
                <option value="sha256"><?php echo $text_sha256; ?></option>
                <?php } ?>
                <?php if ($payfort_fort_entry_hash_algorithm == 'sha512') { ?>
                <option value="sha512" selected="selected"><?php echo $text_sha512; ?></option>
                <?php } else { ?>
                <option value="sha512"><?php echo $text_sha512; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_request_sha_phrase; ?></td>
            <td><input type="text" size ="50" name="payfort_fort_entry_request_sha_phrase" value="<?php echo $payfort_fort_entry_request_sha_phrase; ?>" />
              <?php if ($error_payfort_fort_entry_request_sha_phrase) { ?>
              <span class="error"><?php echo $error_payfort_fort_entry_request_sha_phrase; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_response_sha_phrase; ?></td>
            <td><input type="text" size ="50" name="payfort_fort_entry_response_sha_phrase" value="<?php echo $payfort_fort_entry_response_sha_phrase; ?>" />
              <?php if ($error_payfort_fort_entry_response_sha_phrase) { ?>
              <span class="error"><?php echo $error_payfort_fort_entry_response_sha_phrase; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_sandbox; ?></td>
            <td><?php if ($payfort_fort_entry_sandbox_mode) { ?>
              <input type="radio" name="payfort_fort_entry_sandbox_mode" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="payfort_fort_entry_sandbox_mode" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="payfort_fort_entry_sandbox_mode" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="payfort_fort_entry_sandbox_mode" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td>Host to Host URL:</td>
            <td><input size="50" type="text" readonly="readonly" value="<?php echo $this->url->link('payment/payfort_fort/response');?>"/></td>
          </tr>        
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="payfort_fort_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $payfort_fort_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="payfort_fort_status">
                <?php if ($payfort_fort_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <<tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="payfort_fort_sort_order" value="<?php echo $payfort_fort_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>