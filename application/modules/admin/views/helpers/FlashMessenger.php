<?php
/*
* @category   Avatar Helper modules
 * @package    Avatar Real Estate 
 * @subpackage FlashMessenger
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    Avatar 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses       Helper
 * @category   FlashMessenger
 * @package    Zend_Application
 * @subpackage FlashMessenger
 */
class Zend_View_Helper_FlashMessenger extends Zend_Controller_Action_Helper_Abstract
{
	/*
	 * function for messenger used to display message
	 * @param  Array
	 * @return Array
	 */
	public function flashMessenger()
    {
		$messages 		= Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
		$messages1 		= Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getCurrentMessages();
		$statMessages	= array();
		$output			= '';
		/*if (count($messages) > 0) {
			foreach ($messages as $message) {
				if (!array_key_exists($message['status'], $statMessages)) {                                        
					$statMessages[$message['status']] = array();
				}
				if ($translator != NULL && $translator instanceof Zend_Translate) {
					array_push($statMessages[$message['status']], $translator->_($message['message']));
				} else {
					array_push($statMessages[$message['status']], $message['message']);
				}                                  
			}
			foreach ($statMessages as $status => $messages) {
				$output .= '<div class="' . $status . '">';
				if (count($messages) == 1) {
					$output .=  $messages[0];
				} else {
					$output .= '<ul>';
					foreach ($messages as $message) {
						$output .= '<li>' . $message . '</li>';
					}                                                
					$output .= '</ul>';
				}
				$output .= '</div>';
			}
			
		}*/
		if (count($messages) > 0) {
			foreach ($messages as $message) {
				if(trim($message) != "") {
					$output .= $message;
				}
			}
		} else if(count($messages1) > 0) {
			foreach ($messages1 as $message) {
				if(trim($message) != "") {
					$output .= $message;
				}
			}
		}
		return $output;
    }
}