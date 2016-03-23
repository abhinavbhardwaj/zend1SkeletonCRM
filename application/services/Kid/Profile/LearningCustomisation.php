<?php

/**
 * PHP version 5
 * 
 * @category  Service_Kid
 * @package   Kid
 * @author    Ashwini Asgarwal <ashwini.agarwal@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_Kid_Profile_LearningCustomisation {

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectchild;
    private $_objectParent;
    private $_defaultLC;

    /**
     * construct funtion
     */
    public function __construct() {
        //including functions.php
        include_once APPLICATION_PATH . '/../library/functions.php';
        //including validate.php
        include_once APPLICATION_PATH . '/../library/validate.php';
        // creates object of class child
        $this->_objectchild = new Application_Model_Child();
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
        // creates object of class default learning customization
        $this->_defaultLC = new Application_Model_DefaultLearningCustomization ();
    }

    public function save($data, $userId, $type) {
        if ($type == 'mobile') {
            // getting param access_token
            $accessToken = $data['access_token'];
            // getting param device_key
            $deviceKey = $data['device_key'];
            $objAuth = new Application_Service_User_AuthDevice();
            $validateAuth = $objAuth->authenticate($deviceKey, $accessToken, null);
            if ($validateAuth['status_code'] == STATUS_ERROR) {
                return $validateAuth;
            }
            $parId = $validateAuth['parentId'];
        }
        if ($type == 'web') {
            // getting authenticated user info
            $parId = $this->_objectParent->getParentData($userId, 'parId');
        }
        $childId = $data['childId'];
        $learningtype = strtolower($data['radio-2-set']);
        if ($learningtype == 'part') {
            $data['unlockTime'] = 0;
        }
        $unlockTime = $data['unlockTime'];

        if (($response = $this->validateData($data))) {
            return $this->formatResponse($response, $type);
        }
        try {
            // unlock time - convert hours into mins.
            /* if ($unlockTime > 0 && $unlockTime < 7) {
              $unlockTime = 60 * $unlockTime;
              } */

            if ($type == 'web') {
                $this->saveSubjectData($data);
            }

            $confirmMessage = $this->addlearnigCustomization($childId, $data, $parId);
            $this->saveCustomMessage($data, $parId);
            //formatting child Array
            $childInfoArray = $this->_objectchild->getChildInfoArray(
                    $childId
            );

            //sending push
            $sendPushOnallDevieOfParent = $this->_objectchild
                    ->sendPushOnAddOrUpdateKid($parId, $childInfoArray, 'edit', $childId);
            // block to add or update custome message end
            $messageArray = array(
                'message' => $confirmMessage,
                'status' => 'success',
                'status_code' => '110011',
                'child_id' => base64_encode($childId),
                'goalType' => $learningtype,
                'unlockTime' => $unlockTime,
                'action' => 'edit'
            );
        } catch (Exception $e) {
            $messageArray = array(
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => '110013',
                'child_id' => null,
                'action' => null
            );
        }
        return $this->formatResponse($messageArray, $type);
    }

    public function validateData($data) {

        $message = '';
        //validates childId
        if (empty($message) && !validateNotNull($data['childId'])) {
            $message = "Child id can't be blank";
            $typeError = null;
        }
        // validate blank $askQuesTime
        if (empty($message) && !validateNotNull($data['quesAskTime'])) {
            $message = "Please select question ask time";
        }

        // validate blank $chancesNo
        if (empty($message) && !validateNotNull($data['noChances'])) {
            $message = "Please select number of chances";
        }
        // validate blank $chancesNo
        if (empty($message) && (!validateNotNull($data['unlockTime']) && $data['radio-2-set'] == 'pert')) {
            $message = "Please select time for which device will be locked";
        }

        // validate blank performacne type
        if (empty($message) && (!validateNotNull($data['radio-2-set']))) {
            $message = "Please select goal type";
        }

        // validate blank $chancesNo
        if (empty($message) && !validateNotNull($data['weeklyGoal'])) {
            $message = "Please enter weekly goal";
        }

        // validate blank $chancesNo
        if (empty($message) && $data['weeklyGoal'] == 0) {
            $message = "Weekly goal should be greater than 0";
        }

        // if for validate weekly Goal inside call function validateFieldWithRegex for match with Regexp
        if (empty($message) && !validateFieldWithRegex($data['weeklyGoal'], '/^[0-9]+$/')) {
            $message = "Only digits are allowed in weekly goal";
        }

        // if for validate weekly Goal inside call function validateFieldWithRegex for match with Regexp
        if (empty($message) && strlen($data['weeklyGoal']) > 8) {
            $message = "Only 8 digits are allowed in weekly goal";
        }

        // if for validate weekly Goal inside call function validateFieldWithRegex for match with Regexp
        if (empty($message) && !empty($data['customMessage']) && strlen($data['customMessage']) > 60) {
            $message = "Please enter custom message of max 60 characters";
        }

        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'status' => 'error',
                'childId' => null,
                'status_code' => '110012',
                'action' => null
            );

            return $messageArray;
        }

        return FALSE;
    }

    public function saveSubjectData($data) {
        $arrayIndex = 0;
        foreach ($data['childsubjectData'] as $childSubjectId) {
            $childSubjectListData[$arrayIndex++] = array('subject_id' => $childSubjectId,
                'domainExist' => 'Yes',
                'domainArray' => $data['childSubjectDomain'][$childSubjectId]
            );
        }

        $this->_objectchild->addChildSubject($data['childId'], $data['grade_level'], $childSubjectListData);
    }

    public function saveCustomMessage($data, $userId) {

        $date = todayZendDate();
        $childId = $data['childId'];
        $customMessage = $data['customMessage'];

        $defaultCustomMessage = "Question from finny!";

        // getting child's custom message
        $messageData = $this->_objectchild->getChildsCustomMessage($childId);
        if (empty($messageData)) {
            if (!empty($customMessage) && ($customMessage != $defaultCustomMessage)) {
                $this->_objectchild->addOrUpdateChildsCustomMessage($customMessage, $childId, $userId, $date, "add", null);
            }
        } else {
            $customMessageId = $messageData ['custom_message_id']; // getting message id from custom message
            if (!empty($customMessage) && ($customMessage != $defaultCustomMessage)) {
                $this->_objectchild->addOrUpdateChildsCustomMessage($customMessage, $childId, $userId, $date, "update", $customMessageId);
            } else {
                $this->_objectchild->deleteCustomMessage($customMessageId);
            }
        }
    }

    public function addlearnigCustomization($childId, $learnigCustomizationDataPost) {

        $addGoals = '';
        $formAction = '';
        $tyPe = '';
        $date = todayZendDate(); // getting today data time using function
        $dayOfWeek = date('w');

        $quesAskTime = $learnigCustomizationDataPost ['quesAskTime'];
        $noChances = $learnigCustomizationDataPost ['noChances'];
        $learningType = strtolower($learnigCustomizationDataPost ['radio-2-set']);
        $weeklyGoal = $learnigCustomizationDataPost ['weeklyGoal'];
        $unlockTime = $learnigCustomizationDataPost ['unlockTime'];

        $isInterruption = 'N';
        if (isset($learnigCustomizationDataPost ['checkboxInterruption']) && $learnigCustomizationDataPost ['checkboxInterruption'] == 'Y') {
            $isInterruption = 'Y';
        }

        if ($learningType == 'part') {
            $unlockTime = 0;
        } else {
            $unlockTime = $unlockTime;
        }

        //$tyPe = $learnigCustomizationDataPost ['type'];

        /* $formAction = $learnigCustomizationDataPost ['nextPage'];

          if ($formAction == 'add') {
          $addGoals = 'update';
          } */

        //$add = 'update';
        // Block to add child goals info
        // adds child's goal info

        $addChildGoals = $this->_objectchild->addOrUpdateChildGoal($childId, $quesAskTime, $noChances, $weeklyGoal, $unlockTime, 'update', $learningType, $isInterruption);

        // block for weeekly Goals
        if (!empty($addGoals)) {
            //getting all paired devicelist of child
            $deviceData = $this->_objectDevice->getChildAllUsedDeviceForChild($childId);
            //added/updated weekly goal if there is any paired device for child
            if (!empty($deviceData) && $deviceData != null) {
                $weekOfGoal = weekOfGoalValue($dayOfWeek, $weeklyGoal); // geeting days of week for goal
                $endDateWeekGoal = endDateWeekGoalVal($dayOfWeek); // geeting end date for weekly goal
                $addChildWeekGoals = $this->_objectchild->addOrUpdateChildWeeklyGoal($childId, $weekOfGoal, $date, $endDateWeekGoal, $addGoals, $weeklyGoal);
            }
        }

        /*
          if ($formAction == 'add') { // if to check action is add or edit for add device into db or update.
          if ($tyPe == 'edit') {
          $confirmMessage = "Child learning customization info updated successfully";
          } else {
          $confirmMessage = "Child learning customization info added successfully";
          }
          return $confirmMessage;
          } else if ($formAction == 'edit') {
          $confirmMessage = "Child learning customization info updated successfully";
          return $confirmMessage;
          } else {
          return true;
          } */

        $confirmMessage = "Child learning customization info updated successfully";
        return $confirmMessage;
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
