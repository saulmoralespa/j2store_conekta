<?php
/**
 * --------------------------------------------------------------------------------
 * Payment Plugin - Paymill
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2014-19 J2Store . All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 * */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$jsonarr = json_encode($this->code_arr);
?>
<div class="note">
	<?php echo JText::_($vars->onbeforepayment_text); ?>
	<?php
	$image = $this->params->get('display_image', '');
	?>
	<?php if(!empty($image)): ?>
        <span class="j2store-payment-image">
				<img class="payment-plugin-image payment_cash" src="<?php echo JUri::root().JPath::clean($image); ?>" />
			</span>
	<?php endif; ?>
    <p><strong><?php echo JText::_($vars->display_name); ?></strong></p>
</div>
<form id="conekta-form" action="<?php echo JRoute::_("index.php?option=com_j2store&view=checkout"); ?>" method="post" name="adminForm" enctype="multipart/form-data">
	<?php if ($vars->payment_mode == 'credit') : ?>
        <div class="note">

            <table id="pay_form">
                <tr>
                    <td class="field_name"><?php echo JText::_('J2STORE_CARDHOLDER_NAME') ?></td>
                    <td><?php echo $vars->cardholder; ?></td>
                </tr>

                    <tr>
                        <td class="field_name"><?php echo JText::_('J2STORE_CARD_NUMBER') ?></td>
                        <td>************<?php echo $vars->cardnum; ?></td>
                    </tr>
                    <tr>
                        <td class="field_name"><?php echo JText::_('J2STORE_EXPIRY_DATE') ?></td>
                        <td><?php echo $vars->cardmonth; ?>/<?php echo $vars->cardyear; ?></td>
                    </tr>
                    <tr>
                        <td class="field_name"><?php echo JText::_('J2STORE_CARD_CVV') ?></td>
                        <td>****</td>
                    </tr>
            </table>
        </div>

        <input type="button" onclick="j2storeConektaSubmit(this)" id="conekta-submit-button" class="button btn btn-primary" value="<?php echo JText::_($vars->button_text); ?>" />

        <input type='hidden' name='cardholder' value='<?php echo @$vars->cardholder; ?>' />
        <input type='hidden' name='cardnum' value='<?php echo @$vars->cardnum; ?>' />
        <input type='hidden' name='cardmonth' value='<?php echo @$vars->cardmonth; ?>' />
        <input type='hidden' name='cardyear' value='<?php echo @$vars->cardyear; ?>' />
        <input type='hidden' name='cardcvv' value='<?php echo @$vars->cardcvv; ?>' />
        <input type='hidden' id="conekta-token" name='token' value=''>
	<?php else: ?>
        <input type="button" onclick="j2storeConektaMoneySubmit(this)" id="conekta-submit-button" class="button btn btn-primary" value="<?php echo JText::_($vars->button_text); ?>" />
	<?php endif; ?>
    <input type='hidden' name='payment_mode' value='<?php echo @$vars->payment_mode; ?>' />
    <input type='hidden' name='order_id' value='<?php echo @$vars->order_id; ?>' />
    <input type='hidden' name='orderpayment_id' value='<?php echo @$vars->orderpayment_id; ?>' />
    <input type='hidden' name='orderpayment_type' value='<?php echo @$vars->orderpayment_type; ?>' />
    <input type='hidden' name='option' value='com_j2store' />
    <input type='hidden' name='view' value='checkout' />
    <input type='hidden' name='task' value='confirmPayment' />
    <input type='hidden' name='paction' value='process' />
    <div id="cash_oxxo" style="display: none;">
        <div class="opps">
            <div class="opps-header">
                <div class="opps-reminder">Ficha digital. No es necesario imprimir.</div>
                <div class="opps-info">
                    <div class="opps-brand"><img src="<?php echo JUri::root (true);?>/plugins/j2store/payment_conekta/assets/img/oxxo_pay_Grande.png" alt="OXXOPay"></div>
                    <div class="opps-ammount">
                        <h3>Monto a pagar</h3>
                        <h2 class="mount">$ <b></b><sup></sup></h2>
                        <p>OXXO cobrará una comisión adicional al momento de realizar el pago.</p>
                    </div>
                </div>
                <div class="opps-reference">
                    <h3>Referencia</h3>
                    <h1 class="reference"></h1>
                </div>
            </div>
            <div class="opps-instructions">
                <h3>Instrucciones</h3>
                <ol>
                    <li>Acude a la tienda OXXO más cercana. <a href="https://www.google.com.mx/maps/search/oxxo/" target="_blank">Encuéntrala aquí</a>.</li>
                    <li>Indica en caja que quieres ralizar un pago de <strong>OXXOPay</strong>.</li>
                    <li>Dicta al cajero el número de referencia en esta ficha para que tecleé directamete en la pantalla de venta.</li>
                    <li>Realiza el pago correspondiente con dinero en efectivo.</li>
                    <li>Al confirmar tu pago, el cajero te entregará un comprobante impreso. <strong>En el podrás verificar que se haya realizado correctamente.</strong> Conserva este comprobante de pago.</li>
                </ol>
                <div class="opps-footnote">Al completar estos pasos recibirás un correo de <strong>Nombre del negocio</strong> confirmando tu pago.</div>
            </div>
        </div>
    </div>
    <div id="cash_spei" style="display: none;">
        <div class="ps">
            <div class="ps-header">
                <div class="ps-reminder">Ficha digital. No es necesario imprimir.</div>
                <div class="ps-info">
                    <div class="ps-brand"><img src="<?php echo JUri::root (true);?>/plugins/j2store/payment_conekta/assets/img/spei.png" alt="Banorte"></div>
                    <div class="ps-amount">
                        <h3>Monto a pagar</h3>
                        <h2 class="mount">$ <b></b><sup></sup></h2>
                        <p>Utiliza exactamente esta cantidad al realizar el pago.</p>
                    </div>
                </div>
                <div class="ps-reference">
                    <h3>CLABE</h3>
                    <h1 class="clabe"></h1>
                </div>
            </div>
            <div class="ps-instructions">
                <h3>Instrucciones</h3>
                <ol>
                    <li>Accede a tu banca en línea.</li>
                    <li>Da de alta la CLABE en esta ficha. <strong>El banco deberá de ser STP</strong>.</li>
                    <li>Realiza la transferencia correspondiente por la cantidad exacta en esta ficha, <strong>de lo contrario se rechazará el cargo</strong>.</li>
                    <li>Al confirmar tu pago, el portal de tu banco generará un comprobante digital. <strong>En el podrás verificar que se haya realizado correctamente.</strong> Conserva este comprobante de pago.</li>
                </ol>
                <div class="ps-footnote">Al completar estos pasos recibirás un correo de <strong>Nombre del negocio</strong> confirmando tu pago.</div>
            </div>
        </div>
    </div>
	<?php echo JHTML::_('form.token'); ?>
</form>
<br />
<div class="conekta-payment-errors"></div>
<br />
<div class="plugin_error_div">
    <span class="plugin_error"></span><br>
    <span class="plugin_error_instruction"></span>
</div>
<script type="text/javascript">
    if(typeof(j2store) == 'undefined') {
        var j2store = {};
    }
    if(typeof(j2store.jQuery) == 'undefined') {
        j2store.jQuery = jQuery.noConflict();
    }

    function j2storeConektaSubmit(button) {

        (function($) {
            $(button).attr('disabled', 'disabled');
            $(button).val('<?php echo JText::_('J2STORE_PAYMENT_PROCESSING_PLEASE_WAIT')?>');

            doConektaToken();

        })(j2store.jQuery);
    }

    function j2storeConektaMoneySubmit(button){
        (function($) {
            $(button).attr('disabled', 'disabled');
            $(button).val('<?php echo JText::_('J2STORE_PAYMENT_PROCESSING_PLEASE_WAIT')?>');

            doSendRequest();

        })(j2store.jQuery);
    }

    function doConektaToken() {
        (function($) {
			<?php if($vars->payment_mode == 'credit'): ?>
            var data = {
                "card": {
                    "number": "<?php echo $vars->cardnum; ?>",
                    "name": "<?php echo $vars->cardholder; ?>",
                    "exp_year": "<?php echo $vars->cardyear; ?>",
                    "exp_month": "<?php echo $vars->cardmonth; ?>",
                    "cvc": "<?php echo $vars->cardcvv; ?>"
                }
            };
            try {
                Conekta.Token.create(data, conektaResponseHandler, conektaResponseHandler);
            }catch (e){
                $(".conekta-payment-errors").text(e);
                logResponse(e.message);
            }

			<?php else: ?>
            //diferent method

			<?php endif ; ?>
            return false;

        })(j2store.jQuery);
    }

    function conektaResponseHandler(token) {
        if (token.type) {
            j2store.jQuery(".conekta-payment-errors").addClass('alert alert-error');
            j2store.jQuery(".conekta-payment-errors").text(token.message + ' ' + token.error_code);
        }
        else
        {
            j2store.jQuery('#conekta-form #conekta-token').val(token.id);
            doSendRequest();

        }

    }

    function doSendRequest() {

        (function($) {

            var button = j2store.jQuery('#conekta-submit-button');
            //get all form values
            var form = $('#conekta-form');
            var values = form.serializeArray();

            //submit the form using ajax
            var jqXHR =	$.ajax({
                url: 'index.php',
                type: 'post',
                data: values,
                dataType: 'json',
                beforeSend: function() {
                    $(button).after('<span class="wait">&nbsp;<img src="/media/j2store/images/loader.gif" alt="" /></span>');
                },
                success: function (json) {
                    form.find('.j2success, .j2warning, .j2attention, .j2information, .j2error').remove();
                    console.log(json);

                    if (json['error']) {
                        j2store.jQuery(".conekta-payment-errors").addClass('alert alert-error');
                        jQuery('.conekta-payment-errors').text(json['error']);
                        $(button).val('<?php echo JText::_('J2STORE_PAYMENT_ERROR_PROCESSING')?>');
                    }

                    if (json['redirect']) {
                        $(button).val('<?php echo JText::_('J2STORE_PAYMENT_COMPLETED_PROCESSING')?>');
                        window.location.href = json['redirect'];
                    }

                    if(json['reference']){
                        $('#cash_oxxo').show();
                        $('#cash_oxxo h2.mount b').text(json['mount']);
                        $('#cash_oxxo .reference').text(json['reference']);
                        $('#cash_oxxo sup').text(json['currency']);
                        $(button).val('<?php echo JText::_('J2STORE_PAYMENT_COMPLETED_PROCESSING')?>');
                    }
                    if (json['clabe']){
                        $('#cash_spei').show();
                        $('#cash_spei h2.mount b').text(json['mount']);
                        $('#cash_spei .clabe').text(json['clabe']);
                        $('#cash_spei sup').text(json['currency']);
                        $(button).val('<?php echo JText::_('J2STORE_PAYMENT_COMPLETED_PROCESSING')?>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR.error, textStatus, errorThrown);
                    $(button).val('<?php echo JText::_('J2STORE_PAYMENT_ERROR_PROCESSING')?>');
                }
            });

            jqXHR.always(function() {
                $('.wait').remove();
            });
        })(j2store.jQuery);
    }

    function logResponse(res)
    {
        console.log(res);
		<?php if($vars->sandbox) : ?>
        j2store.jQuery('.debug').text(res).show().fadeOut(3000);
		<?php endif; ?>
    }
</script>