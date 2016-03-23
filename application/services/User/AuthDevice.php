<?php

/**
 * Application_Service_User_ResetPassword File Doc Comment
 * PHP version 5
 * 
 * @category   User
 * @package    User
 * @subpackage To_Add/Update/login/registartion_Info_Of_User
 * @author     Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 * @license    A3logics India (pvt) Ltd
 * @link       http://www.a3logics.com
 * 
 */
class Application_Service_User_AuthDevice {

    /**
     * construct funtion
     */
    public function __construct() {
        
    }

    public function authenticate($deviceKey, $accessToken, $childId = null) {
        // retruns array if any field is not validate
        if (($response = $this->validate($deviceKey, $accessToken)) != FALSE) {
            return $response;
        }

        //check if device is exist in bal_device_info table or not
        $dbDeviceInfo = new Application_Model_DeviceInfo();
        $verifyDeviceInfo = $dbDeviceInfo->verifyDeviceKeyAndAccessToken($deviceKey, $accessToken);
        //if parent_id index of array contains null
        if (empty($verifyDeviceInfo['parentId']) && !empty($verifyDeviceInfo['message'])) {
            $message = $verifyDeviceInfo['message'];
        } else {
            $parentId = $verifyDeviceInfo['parentId'];
            $deviceId = $verifyDeviceInfo['deviceId'];
        }
        //if parentId is emapty
        if (($parentId != '' && $parentId != null) || $parentId != 0) {
            $response = array(
                'parentId' => $parentId,
                'deviceId' => $deviceId,
                'status_code' => STATUS_SUCCESS
            );
            //return $response;
        } else {
            //returns message array
            $response = array(
                'message' => empty($message) ? 'Invalid device key and access token' : $message,
                'status_code' => STATUS_ERROR
            );
        }
        if (empty($childId)) {
            return $response;
        }
        //creats object for class ChildDeviceRelationInfo
        $tblchildDeviceInfo = new Application_Model_DbTable_ChildDeviceRelationInfo();
        //getting child device info
        $childDeviceRelation = $tblchildDeviceInfo->checkDeviceExistOrNotInChildDeviceRelation($deviceId, $childId);
        if (empty($childDeviceRelation) || $childDeviceRelation == null) {
            //returns message array
            $response = array(
                'message' => 'child is not associated with this device',
                'status_code' => STATUS_ERROR
            );
        } else {
            $response = array(
                'parentId' => $parentId,
                'deviceId' => $deviceId,
                'status_code' => STATUS_SUCCESS
            );
        }
        return $response;
    }

    public function getUserId($deviceKey, $accessToken) {
        $response = $this->authenticate($deviceKey, $accessToken);

        if ($response['status_code'] == STATUS_ERROR) {
            return NULL;
        }

        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $parentData = $tblParentInfo->isExistsParentDataWithParId($response['parentId'], 'arrayRow');

        return $parentData->user_id;
    }

    public function validate($deviceKey, $accessToken) {

        $message = null;
        //validate null value for $deviceKey
        if (empty($deviceKey)) {
            $message = "Device key can't be null or empty";
        }

        //validate null value for $deviceName
        if (empty($message) && empty($accessToken)) {
            $message = "Access token can't be null or empty";
        }

        $validateDigits = new Zend_Validate_Digits();
        if (empty($message) && !$validateDigits->isValid($accessToken)) {
            $message = "Only numbers are allowed in access token";
        }

        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'status_code' => STATUS_ERROR
            );

            return $messageArray;
        }

        return $message;
    }

}
