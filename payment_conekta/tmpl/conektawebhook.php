<?php
/**
 * --------------------------------------------------------------------------------
 * Payment Plugin - Coneckta
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      Saul Morales Pacheco <info@saulmoralespa.com>
 * @copyright   Saul Morales Pacheco 2018.
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://saulmoralespa.com
 * --------------------------------------------------------------------------------
 * */
// No direct access to this file
defined('_JEXEC') or die;
/* class JFormFieldFieldtypes extends JFormField */
class JFormFieldConektaWebhook extends JFormField
{
	protected $type = 'conektawebhook';

	public function getInput() {

		$html = '';
		$html .= '<p>'.$this->getTitle();
		$html .= ' <p>' . JURI::root() . 'index.php?option=com_j2store&view=checkout&task=confirmPayment&orderpayment_type=payment_conekta&paction=process_sofort' . '</p>';
		$html .= '</p>';
		return  $html;
	}

	public function getLabel() {
		return '';
	}

}