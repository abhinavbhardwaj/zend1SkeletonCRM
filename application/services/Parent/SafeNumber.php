<?php

/**
 * @author    Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Service_Parent_SafeNumber {

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectParent;

    /**
     * construct funtion
     */
    public function __construct() {
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
    }

    public function save($data, $type = 'web') {

        if ($type == 'mobile') {
            $device_key = !empty($data['device_key']) ? $data['device_key'] : '';
            $access_token = !empty($data['access_token']) ? $data['access_token'] : '';
            $objAuth = new Application_Service_User_AuthDevice();
            $validate = $objAuth->authenticate($device_key, $access_token);
            if ($validate['status_code'] == STATUS_ERROR) {
                return $validate;
            }
            $parId = $validate['parentId'];
            $deviceId = $validate['deviceId'];
        } else {
            $parId = $data['parent_id'];
            $deviceId = NULL;
        }

        if (!empty($data['list'])) {
            $messageArray = array(
                'message' => 'success',
                'status' => 'success',
                'status_code' => STATUS_SUCCESS,
                'data' => $this->_objectParent->getSafeNumbersList($parId, $type)
            );
            return $this->formatResponse($messageArray, $type);
        }

        $editSafeNumberId = !empty($data['editId']) ? $data['editId'] : NULL;
        $deleteA = !empty($data['delete']) ? $data['delete'] : NULL;
        $deleteId = !empty($data['safenumberId']) ? $data['safenumberId'] : NULL;

        // delete Actions
        if (!empty($deleteA)) {
            return $this->formatResponse($this->deleteSafeNumber($parId, $deleteId, $deviceId, $type), $type);
        }

        // edit safe number (get info to show in the form)
        if (!empty($editSafeNumberId)) {
            return $this->formatResponse($this->getSafeNumberDetails($editSafeNumberId), $type);
        }

        $data['safename'] = trim(preg_replace('!\s+!', ' ', $data['safename']));
        if (($response = $this->validateData($data, $parId))) {
            return $this->formatResponse($response, $type);
        }

        $safeName = $data['safename'];
        $code = $data['code'];
        $safeNumber = $data['safeNumber'];
        $safeeditId = !empty($data['safeEditId']) ? $data['safeEditId'] : '';

        //if to check edit safe number or not
        if (empty($safeeditId)) {
            $this->_objectParent->addSafeNumber($parId, $safeName, $safeNumber, $code);
            $message = "Safe number added successfully";
        } else {
            $this->_objectParent->updateSafeNumber($safeName, $safeNumber, $code, $safeeditId);
            $message = "Safe number updated successfully";
        }

        $messageArray = array(
            'message' => $message,
            'parId' => null,
            'status' => 'success',
            'status_code' => STATUS_SUCCESS,
            'data' => $this->_objectParent->getSafeNumbersList($parId, $type)
        );
        $this->sendPush($parId, $deviceId);
        return $this->formatResponse($messageArray, $type);
    }

    public function deleteSafeNumber($parId, $deleteId, $deviceId, $type) {
        if (empty($deleteId)) {
            $messageArray = array(
                'message' => 'safe number id is blank',
                'parId' => null,
                'status' => 'error',
                'status_code' => STATUS_ERROR
            );
        } else {
            $list = $this->_objectParent->getSafeNumbersList($parId, $type);
            if (count($list) <= 1) {
                $messageArray = array(
                    'message' => 'Minimum one safe number is required',
                    'parId' => null,
                    'status' => 'error',
                    'status_code' => STATUS_ERROR
                );
            } else {
                $this->_objectParent->deleteSafeNumber($deleteId);
                $messageArray = array(
                    'message' => "Safe number delete successfully",
                    'parId' => null,
                    'status' => 'success',
                    'status_code' => STATUS_SUCCESS,
                    'data' => $this->_objectParent->getSafeNumbersList($parId, $type)
                );

                $this->sendPush($parId, $deviceId);
            }
        }
        return $messageArray;
    }

    public function getSafeNumberDetails($editSafeNumberId) {
        $safeNumberDataR = $this->_objectParent->getSafeNumberListRow($editSafeNumberId);
        $messageArray = array(
            'message' => "",
            'parId' => null,
            'status' => 'success',
            'title' => $safeNumberDataR['title'],
            'number' => $safeNumberDataR['number'],
            'code' => $safeNumberDataR['country_code'],
            'status_code' => STATUS_SUCCESS
        );

        return $messageArray;
    }

    public function sendPush($parId, $deviceId) {
        $sendNotificationData = array(
            'process_code' => 'safenumber'
        );
        $this->_objectParent->sendPushToAllDevices($parId, $sendNotificationData, $deviceId);
    }

    public function validateData($data, $parId) {
        $safeName = !empty($data['safename']) ? $data['safename'] : '';
        $safeeditId = !empty($data['safeEditId']) ? $data['safeEditId'] : '';
        $safeNumber = !empty($data['safeNumber']) ? $data['safeNumber'] : '';
        $code = !empty($data['code']) ? $data['code'] : '';

        $message = FALSE;
        $nameType = FALSE;
        $phoneType = FALSE;

        if (empty($message) && empty($safeName)) {
            $message = 'Please enter name';
            $nameType = TRUE;
        }

        if (empty($message) && (strlen($safeName) > 128)) {
            $message = 'Maximum 128 characters allowed in name';
            $nameType = TRUE;
        }

        if (empty($message) && empty($safeNumber)) {
            $message = 'Please enter mobile number';
            $phoneType = TRUE;
        }

        if (empty($message) && !is_numeric($safeNumber)) {
            $message = 'Please enter valid mobile number';
            $phoneType = TRUE;
        }

        if (empty($message) && (strlen($safeNumber) < 7 || strlen($safeNumber) > 15)) {
            $message = 'Mobile number length must be in between 7 to 15';
            $phoneType = TRUE;
        }

        if (empty($message) && (empty($code) && !strlen($code))) {
            $message = 'Please enter country code';
            $phoneType = TRUE;
        }

        $dbCountryCode = new Application_Model_CountryCode();
        if (empty($message) && !$dbCountryCode->isValidCountryCode($code)) {
            $message = 'Please enter a valid country code';
            $phoneType = TRUE;
        }

        // block for check safe Title is alerady exist of not remove restrication parent title
        if (empty($message) && $this->_objectParent->checkSafeTitle($safeName, $parId, $safeeditId)) {
            $message = "Safe name already exist";
            $nameType = TRUE;
        }

        // block for check safe number is alerady exist of not
        if (empty($message) && $this->_objectParent->checkSafeNumber($safeNumber, $code, $parId, $safeeditId)) {
            $message = "Mobile number already exist";
            $phoneType = TRUE;
        }

        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'parId' => null,
                'status' => 'error',
                'nameType' => $nameType,
                'phoneType' => $phoneType,
                'status_code' => STATUS_ERROR
            );

            return $messageArray;
        }

        return FALSE;
    }

    public function formatResponse($response, $type) {
        if ($type == 'web') {
            unset($response['status_code']);
        } else {
            unset($response['status']);
        }
        return $response;
    }

}
