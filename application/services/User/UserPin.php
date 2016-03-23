<?php

/**
 * Application_Service_User_UserPin File Doc Comment
 * PHP version 5
 * 
 * @category   User
 * @package    User
 * @subpackage To_Add/Update Pin_Of_User
 * @author     Suman Khatri <suman.khatri@a3logics.in>
 * @license    A3logics India (pvt) Ltd
 * @link       http://www.a3logics.com
 * 
 */
class Application_Service_User_UserPin {

    /**
     * 
     */
    private $_objectParent;

    /**
     * construct funtion
     */
    public function __construct() {
        //including functions.php
        include_once APPLICATION_PATH . '/../library/functions.php';
        //including validate.php
        include_once APPLICATION_PATH . '/../library/validate.php';
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
    }

    public function save($data) {

        $objAuth = new Application_Service_User_AuthDevice();
        $validate = $objAuth->authenticate($data['device_key'], $data['access_token']);
        if ($validate['status_code'] == STATUS_ERROR) {
            return $validate;
        }
        $parId = $validate['parentId'];
        $deviceId = $validate['deviceId'];

        if (($response = $this->validateData($data))) {
            return $response;
        }

        try {
            $tblParentInfo = new Application_Model_DbTable_ParentInfo();
            $parentData = $tblParentInfo->isExistsParentDataWithParId($parId, 'arrayRow');

            $this->_objectParent->updateParentChangePassword(array('pin' => $data['pin']), $parentData->user_id);
            $sendNotificationData = array(
                'process_code' => 'change pin',
                'pin' => $data['pin']
            );
            $this->_objectParent->sendPushToAllDevices($parId, $sendNotificationData, $deviceId);

            $response = array(
                'message' => 'success',
                'status_code' => STATUS_SUCCESS
            );
        } catch (Exception $e) {
            $response = array(
                'message' => $e->getMessage(),
                'status_code' => STATUS_SYSTEM_ERROR
            );
        }
        return $response;
    }

    public function validateData($data) {

        $message = FALSE;
        if (empty($data['pin'])) {
            $message = "Please enter pin";
        }

        //validates digit in pin number
        if (empty($message) && !validateDigits($data['pin'])) {
            $message = "Only numbers are allowed in pin munber";
        }

        //validates min length 
        if (empty($message) && !validateMinLength($data['pin'], '4')) {
            $message = "Please enter pin munber of 4 digits";
        }

        //validates max length 
        if (empty($message) && !validateMaxLength($data['pin'], '4')) {
            $message = "Please enter pin munber of 4 digits";
        }

        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'status_code' => STATUS_ERROR
            );

            return $messageArray;
        }

        return FALSE;
    }

}
