<?php
/**
 * --------------------------------------------------------------------------------
 * Payment Plugin - Coneckta
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      Saul Morales Pacheco <info@saulmoralespa.com>
 * @copyright   Saul Morales Pacheco 2018.
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://saulmoralespa.com
 * --------------------------------------------------------------------------------
 * */

// No direct access

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_j2store/library/plugins/payment.php';
require_once JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2store.php';
class plgJ2StorePayment_conekta extends J2StorePaymentPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 * forcing it to be unique
	 * */

	public $_element = 'payment_conekta';
	private $public_key = '';
	private $private_key = '';
	public $code_arr = array();
	private $_isLog = true;
	var $_j2version = null;

	/**
	 * Constructs a PHP_CodeSniffer object.
	 *
	 * @param   string  $subject  The number of spaces each tab represents.
	 * @param   string  $config   The charset of the sniffed files.
	 *
	 * @see process()
	 * */

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('', JPATH_ADMINISTRATOR);

		$mode = $this->params->get('sandbox', 0);
		if(!$mode) {
			$this->public_key = trim($this->params->get('live_api_public_key'));
			$this->private_key = trim($this->params->get('live_api_private_key'));
		} else {
			$this->public_key = trim($this->params->get('test_api_public_key'));
			$this->private_key = trim($this->params->get('test_api_private_key'));
		}
	}

	/**
	 * Prepares variables and
	 * Renders the form for collecting payment info
	 *
	 * @param   array  $data  form post data.
	 *
	 * @return  string   unknown_type.
	 *
	 * @return  void
	 *
	 * @see process()
	 * */

	public function _renderForm($data)
	{
		$vars = new JObject();
		$vars->prepop = array();
		$vars->public_key = $this->public_key;
		$vars->onselection_text = $this->params->get('onselection', '');
		$html = $this->_getLayout('form', $vars);

		return $html;
	}



	function _verifyForm( $submitted_values )
	{
		$object = new JObject();
		$object->error = false;
		$object->message = '';

		if($submitted_values['conekta_payment_mode'] == 'oxxo' || $submitted_values['conekta_payment_mode'] == 'spei') {
			return $object;
		}elseif ($submitted_values['conekta_payment_mode'] == 'credit'){
			foreach ($submitted_values as $key=>$value)
			{
				switch ($key)
				{
					case "cardholder":
						if (!isset($submitted_values[$key]) || !JString::strlen($submitted_values[$key]))
						{
							$object->error = true;
							$object->message .= "<li>".JText::_( "J2STORE_SAGEPAY_MESSAGE_CARD_HOLDER_NAME_REQUIRED" )."</li>";
						}
						break;
					case "cardnum":
						if (!isset($submitted_values[$key]) || !JString::strlen($submitted_values[$key]))
						{
							$object->error = true;
							$object->message .= "<li>".JText::_( "J2STORE_SAGEPAY_MESSAGE_CARD_NUMBER_INVALID" )."</li>";
						}
						break;
					case "month":
						if (!isset($submitted_values[$key]) || !JString::strlen($submitted_values[$key]))
						{
							$object->error = true;
							$object->message .= "<li>".JText::_( "J2STORE_SAGEPAY_MESSAGE_CARD_EXPIRATION_DATE_INVALID" )."</li>";
						}
						break;
					case "year":
						if (!isset($submitted_values[$key]) || !JString::strlen($submitted_values[$key]))
						{
							$object->error = true;
							$object->message .= "<li>".JText::_( "J2STORE_SAGEPAY_MESSAGE_CARD_EXPIRATION_DATE_INVALID" )."</li>";
						}
						break;
					case "cardcvv":
						if (!isset($submitted_values[$key]) || !JString::strlen($submitted_values[$key]))
						{
							$object->error = true;
							$object->message .= "<li>".JText::_( "J2STORE_SAGEPAY_MESSAGE_CARD_CVV_INVALID" )."</li>";
						}
						break;
					default:
						break;
				}
			}
		}
		return $object;
	}


	/**
	 * set currency and amount.
	 *
	 * @param   array  $data  form post data.
	 *
	 * @return  string   HTML to display
	 * @return  void
	 *
	 * @see process()
	 * */

	public function _prePayment( $data )
	{
		$app = JFactory::getApplication();
		$currency = J2Store::currency();

		// Prepare the payment form
		$vars = new JObject;


		$vars->url = JRoute::_("index.php?option=com_j2store&view=checkout");
		$vars->order_id = $data['order_id'];
		$vars->orderpayment_id = $data['orderpayment_id'];
		$vars->orderpayment_type = $this->_element;

		F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
		$order = F0FTable::getInstance('Order', 'J2StoreTable');
		$order->load($data['orderpayment_id']);

		$currency_values= $this->getCurrency($order);
		$amount = J2Store::currency()->format($order->order_total, $currency_values['currency_code'], $currency_values['currency_value'], false);
		$vars->amount = $amount*100;
		$vars->currency_code =$currency_values['currency_code'];
		$vars->payment_mode = $app->input->getString('conekta_payment_mode');



		$vars->cardholder = $app->input->getString("cardholder");
		// Cerdit card
		$vars->cardnum = $app->input->getString("cardnum");
		$vars->cardmonth = $app->input->getString("month");
		$vars->cardyear = $app->input->getString("year");

		$vars->cardcvv = $app->input->getString("cardcvv");


		$vars->public_key = $this->public_key;
		$vars->private_key = $this->private_key;

		$vars->display_name = $this->params->get('display_name', 'PLG_J2STORE_PAYMENT_PAYMILL');
		$vars->onbeforepayment_text = $this->params->get('onbeforepayment', '');
		$vars->button_text = $this->params->get('button_text', 'J2STORE_PLACE_ORDER');
		$vars->sandbox = $this->params->get('sandbox', 0);
		// Lets check the values submitted
		$html = $this->_getLayout('prepayment', $vars);

		return $html;
	}

	/**
	 * Processes the payment form
	 * and returns HTML to be displayed to the user
	 * generally with a success/failed message
	 *
	 * @param   array  $data  form post data.
	 *
	 * @return  string   HTML to display
	 * @return  void
	 *
	 * @see process()
	 * */

	public function _postPayment( $data )
	{
		// Process the payment
		$app = JFactory::getApplication();
		$vars = new JObject();
		$paction = $app->input->getString('paction');

		switch ($paction)
		{
			case 'process_sofort':
				$this->_process_sofort();
				break;
			case 'cancel':
				$vars->message = JText::_($this->params->get('oncancelpayment', ''));
				$html = $this->_getLayout('message', $vars);
				break;
			case 'display':
				$html = JText::_($this->params->get('onafterpayment', ''));
				$html .= $this->_displayArticle();
				break;
			case 'process':
				$result = $this->_process();
				echo json_encode($result);
				$app->close();
				break;
			default:
				$vars->message = JText::_($this->params->get('onerrorpayment', ''));
				$html = $this->_getLayout('message', $vars);
				break;
		}

		return $html;
	}

	public function _process_sofort()
	{

		$body = @file_get_contents('php://input');
		$data = json_decode($body);
		http_response_code(200); // Return 200 OK

		if ($data->type == 'order.paid'){
			$order_id = $data->data->object->metadata->reference;
			F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
			$order = F0FTable::getInstance ( 'Order', 'J2StoreTable' )->getClone();
			$order->load(array(
				'order_id' => $order_id
			));

			$order->transaction_id = $data->data->object->id;
			$order->transaction_details = $data->data->object->description;
			$order->transaction_status = $data->data->object->status;
			$order->payment_complete ();
			$order->empty_cart();
		}
	}

	/**
	 * Processes the payment
	 * This method process only real time (simple) payments
	 *
	 * @return string unknown_type.
	 *
	 * @return string
	 *
	 * @access protected
	 *
	 */
	public function _process() {
		if (! JRequest::checkToken ()) {
			return $this->_renderHtml ( JText::_ ( 'J2STORE_CONEKTA_INVALID_TOKEN' ) );
		}

		$app = JFactory::getApplication ();
		$data = $app->input->getArray ( $_POST );
		$json = array ();
		$errors = array ();

		// Get order information
		F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
		$order = F0FTable::getInstance ( 'Order', 'J2StoreTable' );
		if ($order->load ( array (
			'order_id' => $data ['order_id']
		) )) {


			$currency_values = $this->getCurrency ( $order );
			$amount = (int)J2Store::currency()->format( $order->order_total, $currency_values ['currency_code'], $currency_values ['currency_value'], false ) * 100;
			$currency_values= $this->getCurrency($order);

			$items = $order->getItems();
			$nameProducts = '';
			$product_id = array();

			foreach ($items as $item)
			{
				$name = $item->orderitem_name;
				$name = str_replace("'", '', $name);
				$nameProducts .= html_entity_decode($name,ENT_QUOTES, 'UTF-8') . '_';
				$product_id .= !empty($item->orderitem_sku)? $item->orderitem_sku . ', ' : $item->product_id . ', ';
			}

			$nameProducts = trim($nameProducts, '_');
			$product_id = trim($product_id, ',');


			$orderinfo = $order->getOrderInformation();
			$countryShipping = empty($orderinfo->shipping_country_id) ? $orderinfo->billing_country_id : $orderinfo->shipping_country_id;
			$country = $this->getCountryById($countryShipping)->country_isocode_2;
			$address = empty($orderinfo->shipping_address_1) ? $orderinfo->billing_address_1 : $orderinfo->shipping_address_1 ;
			$postalCode = empty($orderinfo->shipping_zip) ? $orderinfo->billing_zip : $orderinfo->shipping_zip;
			$shipping_method = empty($orderinfo->shipping_method) ? 'none' : $orderinfo->shipping_method;
			$nameClient = empty($orderinfo->shipping_first_name) ? $orderinfo->billing_first_name : $orderinfo->shipping_first_name;
			$orderShipping = (int)$order->order_shipping;


			require (JPath::clean ( dirname ( __FILE__ ) . "/library/conekta-php/lib/Conekta.php" ));
			\Conekta\Conekta::setApiKey($this->private_key);
			\Conekta\Conekta::setApiVersion("2.0.0");


			if($data['payment_mode'] == 'credit'){
				if (empty ( $data ['token'] )) {
					$json ['error'] = JText::_ ( 'J2STORE_CONEKTA_TOKEN_MISSING' );
					return $json;
				}
					try {
						$customer = \Conekta\Customer::create(
							array(
								"name" => $data ['cardholder'],
								"email" => $order->user_email,
								"phone" => $orderinfo->billing_phone_2,
								"payment_sources" => array(
									array(
										"type" => "card",
										"token_id" => $data ['token']
									)
								)
							)
						);
					} catch (\Conekta\ProcessingError $error){
						$errors [] = $error->getMessage();
					} catch (\Conekta\ParameterValidationError $error){
						$errors [] = $error->getMessage();
					} catch (\Conekta\Handler $error){
						$errors [] = $error->getMessage();
					}

					if(isset($customer->id)){

						try{
							$orderConekta = \Conekta\Order::create(
								array(
									"line_items" => array(
										array(
											"name" => $nameProducts,
											"unit_price" => $amount,
											"quantity" => 1,
											"sku" => $product_id
										)
									),
									"shipping_lines" => array(
										array(
											"amount" => $orderShipping,
											"carrier" => $shipping_method
										)
									),
									"currency" => $currency_values['currency_code'],
									"customer_info" => array(
										"customer_id" => $customer->id
									),
									"shipping_contact" => array(
										"address" => array(
											"street1" => $address,
											"postal_code" => $postalCode,
											"country" => $country
										)
									),
									"metadata" => array("reference" => $order->order_id),
									"charges" => array(
										array(
											"payment_method" => array(
												"type" => "default"
											)
										)
									)
								)
							);
							if(isset($orderConekta->payment_status)){
								$status = $orderConekta->payment_status;
								switch ($status){
									case 'paid':
										$order->payment_complete();
										break;
									case 'payment_pending':
										$order->update_status ( $status );
										break;
									case 'pre_authorized y voided':
										$order->update_status ( $status );
										break;
									case 'declined':
										$order->update_status ( $status );
								}

								if (! $order->store()) {
									$errors [] = $order->getError ();
								} else {
									$order->empty_cart();
								}

							}

						} catch (\Conekta\ProcessingError $error){
							$errMsg = $error->getMesage();
							$errors [] = $errMsg;
							$this->_log ( $errMsg, 'payment response error' );
						} catch (\Conekta\ParameterValidationError $error){
							$errMsg = $error->getMessage();
							$errors [] = $errMsg;
							$this->_log ( $errMsg, 'payment response error' );
						} catch (\Conekta\Handler $error){
							$errMsg = $error->getMesage();
							$errors [] = $errMsg;
							$this->_log ( $errMsg, 'payment response error' );
						}

					}
				if (empty ( $errors )) {
					$json ['success'] = JText::_ ( $this->params->get ( 'onafterpayment', '' ) );
					$json ['redirect'] = JRoute::_ ( 'index.php?option=com_j2store&view=checkout&task=confirmPayment&orderpayment_type=' . $this->_element . '&paction=display' );
				}

			}elseif ($data['payment_mode'] == 'oxxo'){
				try{
					$orderConekta = \Conekta\Order::create(
						array(
							"line_items" => array(
								array(
									"name" => $nameProducts,
									"unit_price" => $amount,
									"quantity" => 1,
									"sku" => $product_id
								)
							),
							"shipping_lines" => array(
								array(
									"amount" => $orderShipping,
									"carrier" => $shipping_method
								)
							),
							"currency" => $currency_values['currency_code'],
							"customer_info" => array(
								"name" => $nameClient,
								"email" => $order->user_email,
								"phone" => $orderinfo->billing_phone_2
							),
							"shipping_contact" => array(
								"address" => array(
									"street1" => $address,
									"postal_code" => $postalCode,
									"country" => $country
								)
							),
							"metadata" => array("reference" => $order->order_id),
							"charges" => array(
								array(
									"payment_method" => array(
										"type" => "oxxo_cash"
									)
								)
							)
						)
					);


					if(isset($orderConekta->payment_status)){
						$status = $orderConekta->payment_status;
						switch ($status){
							case 'payment_pending':
								$order->update_status ( 4 );
								break;
							case 'pre_authorized y voided':
								$order->update_status ( 3 );
								break;
							case 'declined':
								$order->update_status ( 3 );
						}

						if (! $order->store()) {
							$errors [] = $order->getError ();
						} else {
							$order->empty_cart();
						}

					}

					if(isset($orderConekta)){
						$json ['mount'] = $orderConekta->amount/100;
						$json ['reference'] = $orderConekta->charges[0]->payment_method->reference;
						$json ['currency'] = $orderConekta->currency;
					}

				} catch (\Conekta\ParameterValidationError $error){
					$errMsg = $error->getMesage();
					$errors [] = $errMsg;
					$this->_log ( $errMsg, 'payment response error' );
				} catch (\Conekta\Handler $error){
					$errMsg = $error->getMesage();
					$errors [] = $errMsg;
					$this->_log ( $errMsg, 'payment response error' );
				}

			}elseif ($data['payment_mode'] == 'spei'){
				try{
					$orderConekta = \Conekta\Order::create(
						array(
							"line_items" => array(
								array(
									"name" => $nameProducts,
									"unit_price" => $amount,
									"quantity" => 1,
									"sku" => $product_id
								)
							),
							"shipping_lines" => array(
								array(
									"amount" => $orderShipping,
									"carrier" => $shipping_method
								)
							),
							"currency" => $currency_values['currency_code'],
							"customer_info" => array(
								"name" => $nameClient,
								"email" => $order->user_email,
								"phone" => $orderinfo->billing_phone_2
							),
							"shipping_contact" => array(
								"address" => array(
									"street1" => $address,
									"postal_code" => $postalCode,
									"country" => $country
								)
							),
							"metadata" => array("reference" => $order->order_id),
							"charges" => array(
								array(
									"payment_method" => array(
										"type" => "spei"
									)
								)
							)
						)
					);

					if(isset($orderConekta->payment_status)){
						$status = $orderConekta->payment_status;
						switch ($status){
							case 'payment_pending':
								$order->update_status ( 4 );
								break;
							case 'pre_authorized y voided':
								$order->update_status ( 3 );
								break;
							case 'declined':
								$order->update_status ( 3 );
						}

						if (! $order->store()) {
							$errors [] = $order->getError ();
						} else {
							$order->empty_cart();
						}

					}

					if(isset($orderConekta)){
						$json ['mount'] = $orderConekta->amount/100;
						$json ['currency'] = $orderConekta->currency;

						/**
						 * No accces return null
						 * $orderConekta->charges->data[0]->payment_method->receiving_account_number;
						 *
						 * solution temporal encode and decode in array
						 */

						$orderConekta = json_encode($orderConekta);
						$orderConekta = json_decode($orderConekta, true);

						$json ['clabe'] = $orderConekta['charges']['data'][0]['payment_method']['clabe'];
					}

				} catch (\Conekta\ParameterValidationError $error){
					$errMsg = $error->getMesage();
					$errors [] = $errMsg;
					$this->_log ( $errMsg, 'payment response error' );
				} catch (\Conekta\Handler $error){
					$errMsg = $error->getMesage();
					$errors [] = $errMsg;
					$this->_log ( $errMsg, 'payment response error' );
				}

			}


			if (count ( $errors )) {
				$json ['error'] = implode ( "\n", $errors );
			}
		} else {
			$json ['error'] = JText::_ ( 'J2STORE_CONEKTA_INVALID_ORDER' );
		}

		return $json;

	}


	/**
	 * Simple logger
	 *
	 * @param   string  $text  text
	 * @param   string  $type  message
	 *
	 * @return void
	 *
	 * @access protected
	 * */

	public function _log($text, $type = 'message')
	{
		if ($this->_isLog)
			:
			{
				$file = JPATH_ROOT . "/cache/{$this->_element}.log";
				$date = JFactory::getDate();

				$f = fopen($file, 'a');
				fwrite($f, "\n\n" . $date->format('Y-m-d H:i:s'));
				fwrite($f, "\n" . $type . ': ' . $text);
				fclose($f);
			}
		endif;
	}


}