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
class JFormFieldConektaRegistration extends JFormField
{
	protected $type = 'conektaregistration';
	
	public function getInput() {
		
		$html = '';
		$html .= '<p>'.$this->getTitle();
		$html .= ' <a href="https://auth.conekta.com/sign_up" target="_blank" >'.JText::_('J2STORE_REGISTER').'</a>';
		$html .= '</p>';
		return  $html;
	}
	
	public function getLabel() {
		return '';
	}
	
}