<?php

/**
 * Description of FlashMessages
 *
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class My_View_Helper_FlashMessages extends Zend_View_Helper_Abstract {

    public function flashMessages() {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (empty($messages)) {
            $messages = array();
        }

        foreach ($messages as $message) {
            switch (key($message)) {
                case 'success':
                    $class = 'alert-success';
                    $fontAwesome = '<i class="fa fa-check-circle">&nbsp;&nbsp;</i>';
                    break;
                case 'error':
                    $class = 'alert-error';
                    $fontAwesome = '<i class="fa fa-times-circle">&nbsp;&nbsp;</i>';
                    break;
                default:
                    $class = 'alert-info';
                    $fontAwesome = '<i class="fa fa-check-circle">&nbsp;&nbsp;</i>';
                    break;
            }

            $baseUrlHelper = new Zend_View_Helper_BaseUrl();
            $imagePath = $baseUrlHelper->baseUrl('images/close-arrow.png');
            $output .=  '<div class="alert ' . $class . '"><div style="float:left;">' . current($message) . '</div><div style="clear:both;"></div><a class="alert-close loginMsg" href="javascript:void(0);"><img src="'.$imagePath.'" alt="close"></a></div>';
        }

        return $output;
    }

}