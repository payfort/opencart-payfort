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
            <div id="htabs" class="htabs">
                <a href="#tab-general"><?php echo $tab_general; ?></a>
                <a href="#tab-cc"><?php echo $tab_credit_card; ?></a>
                <a href="#tab-installments"><?php echo $tab_installments; ?></a>
                <a href="#tab-sadad"><?php echo $tab_sadad; ?>
                <a href="#tab-naps"><?php echo $tab_naps; ?></a>
            </div>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div id="tab-general">
                    <table class="form">
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td><select name="payfort_fort_status">
                                    <option value="1" <?php echo ($payfort_fort_status) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_status) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_language; ?></td>
                            <td>
                                <select name="payfort_fort_entry_language">
                                    <option value="store" <?php echo ($payfort_fort_entry_language == 'store') ? 'selected="selected"' : '' ?>><?php echo $text_store_language; ?></option>
                                    <option value="en" <?php echo ($payfort_fort_entry_language == 'en') ? 'selected="selected"' : '' ?>><?php echo $text_en; ?></option>
                                    <option value="ar" <?php echo ($payfort_fort_entry_language == 'ar') ? 'selected="selected"' : '' ?>><?php echo $text_ar; ?></option>
                                </select>
                            </td>
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
                            <td>
                                <select name="payfort_fort_entry_command">
                                    <option value="PURCHASE" <?php echo ($payfort_fort_entry_command == 'PURCHASE') ? 'selected="selected"' : '' ?>><?php echo $text_purchase; ?></option>
                                    <option value="AUTHORIZATION" <?php echo ($payfort_fort_entry_command == 'AUTHORIZATION') ? 'selected="selected"' : '' ?>><?php echo $text_authorization; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_hash_algorithm; ?></td>
                            <td>
                                <select name="payfort_fort_entry_hash_algorithm">
                                    <option value="sha1" <?php echo ($payfort_fort_entry_hash_algorithm == 'sha1') ? 'selected="selected"' : '' ?>><?php echo $text_sha1; ?></option>
                                    <option value="sha256" <?php echo ($payfort_fort_entry_hash_algorithm == 'sha256') ? 'selected="selected"' : '' ?>><?php echo $text_sha256; ?></option>
                                    <option value="sha512" <?php echo ($payfort_fort_entry_hash_algorithm == 'sha512') ? 'selected="selected"' : '' ?>><?php echo $text_sha512; ?></option>                                    
                                </select>
                            </td>
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
                            <td>
                                <label class="radio-inline">
                                    <input type="radio" name="payfort_fort_entry_sandbox_mode" value="1" <?php echo ($payfort_fort_entry_sandbox_mode) ? 'checked="checked"' : '' ?> class=""/>
                                    <?php echo $text_yes; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payfort_fort_entry_sandbox_mode" value="0" <?php echo (!$payfort_fort_entry_sandbox_mode) ? 'checked="checked"' : '' ?> class="" />
                                    <?php echo $text_no; ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_gateway_currency; ?><br /><span class="help"><?php echo $help_gateway_currency; ?></span></td>
                            <td>
                                <select name="payfort_fort_entry_gateway_currency">
                                    <option value="base" <?php echo ($payfort_fort_entry_gateway_currency == 'base') ? 'selected="selected"' : '' ?>><?php echo $text_base_currency; ?></option>
                                    <option value="front" <?php echo ($payfort_fort_entry_gateway_currency == 'front') ? 'selected="selected"' : '' ?>><?php echo $text_front_currency ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_debug; ?><br /><span class="help"><?php echo $help_debug; ?></span></td>
                            <td>
                                <select name="payfort_fort_debug">
                                    <option value="1" <?php echo ($payfort_fort_debug) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_debug) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Host to Host URL:</td>
                            <td><input size="90" type="text" readonly="readonly" value="<?php echo $host_to_host_url;?>"/></td>
                        </tr>        
                        <tr>
                            <td><?php echo $entry_order_status; ?></td>
                            <td>
                                <select name="payfort_fort_order_status_id">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $payfort_fort_order_status_id) { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_order_placement; ?><br /><span class="help"><?php echo $help_order_placement; ?></span></td>
                            <td>
                                <select name="payfort_fort_order_placement">
                                    <option value="all" <?php echo ($payfort_fort_order_placement == 'all') ? 'selected="selected"' : '' ?>><?php echo $text_always; ?></option>
                                    <option value="success" <?php echo ($payfort_fort_order_placement == 'success') ? 'selected="selected"' : '' ?>><?php echo $text_on_success; ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tab-cc">
                    <table class="form">
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td>
                                <select name="payfort_fort_credit_card">
                                    <option value="1" <?php echo ($payfort_fort_credit_card) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_credit_card) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                                <?php if ($payfort_fort_payment_method_required) { ?>
                                <span class="error"><?php echo $payfort_fort_payment_method_required; ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_cc_integration_type; ?><br /><span class="help"><?php echo $help_cc_integration_type; ?></span></td>
                            <td>
                                <select name="payfort_fort_cc_integration_type">
                                    <option value="redirection" <?php echo ($payfort_fort_cc_integration_type == 'redirection') ? 'selected="selected"' : '' ?>><?php echo $text_redirection; ?></option>
                                    <option value="merchantPage" <?php echo ($payfort_fort_cc_integration_type == 'merchantPage') ? 'selected="selected"' : '' ?>><?php echo $text_merchant_page; ?></option>
                                    <option value="merchantPage2" <?php echo ($payfort_fort_cc_integration_type == 'merchantPage2') ? 'selected="selected"' : '' ?>><?php echo $text_merchant_page2; ?></option>
                                </select>
                            </td>
                        </tr>
                         
                         
                         <tr>
                            <td><?php echo $entry_cc_mada_branding; ?><br /><span class="help"><?php echo $help_cc_mada_branding; ?></span></td>
                            <td>
                                <select name="payfort_fort_cc_mada_branding">
                     	           <option value="Disabled" <?php echo ($payfort_fort_cc_mada_branding == 'Disabled') ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                   <option value="Enabled"  <?php echo ($payfort_fort_cc_mada_branding == 'Enabled') ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>                                        
                                </select>
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <td><?php echo $entry_sort_order; ?></td>
                            <td><input type="text" name="payfort_fort_sort_order" value="<?php echo $payfort_fort_sort_order; ?>" size="1" /></td>
                        </tr>
                    </table>
                </div>
                
                <div id="tab-installments">
                    <table class="form">
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td>
                                <select name="payfort_fort_installments">
                                    <option value="1" <?php echo ($payfort_fort_installments) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_installments) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                                <?php if ($payfort_fort_payment_method_required) { ?>
                                <span class="error"><?php echo $payfort_fort_payment_method_required; ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_installments_integration_type; ?><br /><span class="help"><?php echo $help_installments_integration_type; ?></span></td>
                            <td>
                                <select name="payfort_fort_installments_integration_type">
                                    <option value="redirection" <?php echo ($payfort_fort_installments_integration_type == 'redirection') ? 'selected="selected"' : '' ?>><?php echo $text_redirection; ?></option>
                                    <option value="merchantPage" <?php echo ($payfort_fort_installments_integration_type == 'merchantPage') ? 'selected="selected"' : '' ?>><?php echo $text_merchant_page; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_sort_order; ?></td>
                            <td><input type="text" name="payfort_fort_installments_sort_order" value="<?php echo $payfort_fort_installments_sort_order; ?>" size="1" /></td>
                        </tr>
                    </table>
                </div>
                
                <div id="tab-sadad">
                    <table class="form">
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td>
                                <select name="payfort_fort_sadad">
                                    <option value="1" <?php echo ($payfort_fort_sadad) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_sadad) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_sort_order; ?></td>
                            <td><input type="text" name="payfort_fort_sadad_sort_order" value="<?php echo $payfort_fort_sadad_sort_order; ?>" size="1" /></td>
                        </tr>
                    </table>
                </div>
                <div id="tab-naps">
                    <table class="form">
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td>
                                <select name="payfort_fort_naps">
                                    <option value="1" <?php echo ($payfort_fort_naps) ? 'selected="selected"' : '' ?>><?php echo $text_enabled; ?></option>
                                    <option value="0" <?php echo (!$payfort_fort_naps) ? 'selected="selected"' : '' ?>><?php echo $text_disabled; ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_sort_order; ?></td>
                            <td><input type="text" name="payfort_fort_qpay_sort_order" value="<?php echo $payfort_fort_qpay_sort_order; ?>" size="1" /></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    $('#htabs a').tabs();
//--></script>
<?php echo $footer; ?>