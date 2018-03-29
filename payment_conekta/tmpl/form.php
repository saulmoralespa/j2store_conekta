<?php
/*------------------------------------------------------------------------
# com_j2store - J2Store
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/


defined('_JEXEC') or die('Restricted access'); ?>
<div class="note">
	<p><?php echo JText::_($vars->onselection_text); ?></p>
</div>
<div class="j2store">
	<div id="field">
<h2><?php echo JText::_("J2STORE_CONEKTA_PAYMENT_MEDIA"); ?></h2>
		<div id="payments_conekta"><img src="<?php echo JUri::root(true);?>/plugins/j2store/payment_conekta/assets/img/credit-card.png" class="change-cash gray-scale" data-type="CREDIT" alt="credit" style="width:100px;height:50px;margin-left:15px; margin-bottom:15px;border:#a2a2a2 solid 1px;cursor:pointer;"><img src="<?php echo JUri::root (true);?>/plugins/j2store/payment_conekta/assets/img/oxxo_pay_Grande.png" class="change-cash gray-scale" data-type="OXXOPAY" alt="OXXO PAY" style="width:100px;height:50px;margin-left:15px; margin-bottom:15px;border:#a2a2a2 solid 1px;cursor:pointer;"><img src="<?php echo JUri::root (true);?>/plugins/j2store/payment_conekta/assets/img/spei.png" class="change-cash gray-scale" data-type="SPEI" alt="SPEI" style="width:100px;height:50px;margin-left:15px;margin-bottom:15px;border:#a2a2a2 solid 1px;cursor:pointer;"></div>
        <div id="conekta_card_holder" style="display: none;">
            <div class="control-group">
                <label class="control-label"><?php echo JText::_( 'J2STORE_CONEKTA_PAYMENT_CARD_HOLDER' ); ?></label>
                <div class="controls"><input size="20" data-conekta="card[name]" name="cardholder" class="required" type="text"></div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo JText::_( 'J2STORE_CONEKTA_PAYMENT_CARD_NUMBER' ); ?></label>
                <div class="controls"><input size="20" data-conekta="card[number]" name="cardnum" class="required number" type="text"></div>
            </div>
            <div class="control-group">
                <label class="control-label">CVC</label>
                <div class="controls"><input size="4" data-conekta="card[cvc]" name="cardcvv"  class="required number" type="text"></div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo JText::_( 'J2STORE_EXPIRY_DATE' ) ?></label>
                <div class="controls">
                    <select name="month" class="required number" title="<?php echo JText::_('J2STORE_EXPIRY_VALIDATION_ERROR_MONTH'); ?>">
                        <option value=""><?php echo JText::_('J2STORE_EXPIRY_MONTH'); ?></option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    <select name="year" class="required number"
                            title="<?php echo JText::_('J2STORE_EXPIRY_VALIDATION_ERROR_YEAR'); ?>"
                    >
                        <option value=""><?php echo JText::_('J2STORE_EXPIRY_YEAR'); ?></option>
		                <?php
		                $two_digit_year = date('y');
		                $four_digit_year = date('Y');
		                ?>
		                <?php for($i=$two_digit_year;$i<$two_digit_year+50;$i++) {?>
                            <option value="<?php echo $i;?>"><?php echo $four_digit_year;?></option>
			                <?php
			                $four_digit_year++;
		                } ?>
                    </select>
                    <input type="hidden" name="conekta_payment_mode" value="credit">
                </div>
        </div>
        </div>
        <div id="conekta_cash">
        </div>
    </div>
</div>
<script type="text/javascript">
    var successHandler;
    var errorHandler;
    var cssId = 'conekta';  // you could encode the css path itself to generate id..
    if (!document.getElementById(cssId))
    {
        var link  = document.createElement('link');
        link.id   = cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = '<?php echo JUri::root();?>/plugins/j2store/payment_conekta/assets/css/oxxo.css';
        link.media = 'all';
        document.getElementsByTagName('head')[0].appendChild(link);
    }

    var script = document.createElement('script');
    script.src = "https://cdn.conekta.io/js/latest/conekta.js";
    script.async = true;
    document.getElementsByTagName('head')[0].appendChild(script);
    script.onload = function() {

        Conekta.setPublicKey('<?php echo $vars->public_key; ?>');
        Conekta.setLanguage("es");


        successHandler = function(token) {
            /* token keys: id, livemode, used, object */
            return token;
        };

        errorHandler = function(err) {
            /* err keys: object, type, message, message_to_purchaser, param, code */
           return err;
        };

    };

    greyChange();
    var htmlFormCard = jQuery('#conekta_card_holder').html();
    jQuery("#payments_conekta img").click(function() {
        var type = jQuery(this).attr('data-type');
        sendTypePayment(type);
        jQuery(this).css("filter","grayscale(0%)");
        greyChange(type);
    });
    function greyChange(type = ''){
        if (type){
            jQuery("#payments_conekta img").not("[data-type="+type+"]").css("filter" , "grayscale(100%)");
        }else{
            jQuery("#payments_conekta img").css("filter" , "grayscale(100%)");
        }
    }
    function sendTypePayment(type){
        if (type === 'CREDIT'){
            jQuery("#conekta_card_holder").html(htmlFormCard).show();
            jQuery("#conekta_oxxo").html('');
        }else if(type === 'OXXOPAY'){
            jQuery("#conekta_card_holder").html('');
            jQuery("#conekta_cash").html('<input type="hidden" name="conekta_payment_mode" value="oxxo">');
        }else if(type === 'SPEI'){
            jQuery("#conekta_card_holder").html('');
            jQuery("#conekta_cash").html('<input type="hidden" name="conekta_payment_mode" value="spei">');
        }
    }
</script>