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
class Application_Service_Kid_Profile_KidInfo
{

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectchild;
    private $_objectParent;
    private $_objectDeviceInfo;

    /**
     * construct funtion
     */
    public function __construct()
    {
        //including functions.php
        include_once APPLICATION_PATH . '/../library/functions.php';
        //including validate.php
        include_once APPLICATION_PATH . '/../library/validate.php';
        // creates object of class child
        $this->_objectchild = new Application_Model_Child();
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
        // creates object of class DeviceInfo
        $this->_objectDeviceInfo = new Application_Model_DeviceInfo();
    }

    public function save($data, $type, $userId)
    {
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

        if (($response = $this->validateData($data, $parId))) {
            return $this->formatResponse($response, $type);
        }
        $childBasicInfo = $this->formatData($data, $parId, $type);
        $childId = (empty($data['childId'])) ? NULL : $data['childId'];
        $gradeId = $data['grade_level'];

        $childInfoArray = $this->_objectchild->getChildInfoArray($childId);
        if (!empty($childInfoArray[0]['dob'])) {
            $childBasicInfo['dob'] = date('Y-m-d', strtotime($childInfoArray[0]['dob']));
        }
        if ($childInfoArray[0]['coppa_required'] && !$childInfoArray[0]['coppa_accepted']) {
            $childBasicInfo['dob'] = NULL;
            $childBasicInfo['gender'] = NULL;
            $childBasicInfo['track_location'] = FALSE;
            unset($childBasicInfo['image']);
        }

        try {
            // add or updates childs basic info
            $res = $this->_objectchild->addOrUpdateChildBasicInfo($childId, $childBasicInfo);
            if (!empty($childId)) {
                // getting grade of child
                $gradeChange = $this->checkGradeChange($childId, $gradeId) ? 'yes' : 'no';
                $res = $childId; // assigning child id to $res if childId is not empty
            } else {
                // updates grade in table gradepoint
                $this->_objectchild->insertGradePoint($res, $gradeId, null);
                $childId = $res;
            }

            $this->addLearningCustomisation($res, $gradeId);
            //formatting child Array
            $childInfoArray = $this->_objectchild->getChildInfoArray($childId);

            //sending push
            $this->_objectchild->sendPushOnAddOrUpdateKid($parId, $childInfoArray, 'edit', $childId, null);

            $childName = trim($data['firstname']);
            if (strlen(html_entity_decode($childName)) > 17) {
                $childName = htmlentities(substr(html_entity_decode($childName), 0, 16)) . '...';
            }

            $messageArray = array(
                'message' => "Child info updated successfully",
                'status' => 'success',
                'status_code' => '110011',
                'child_id' => base64_encode($childId),
                'gradeChange' => $gradeChange,
                'typeerror' => null,
                'childName' => $childName
            );
        } catch (Exception $e) {
            $messageArray = array(
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => '110013',
                'child_id' => null,
                'gradeChange' => null,
                'action' => null,
                'typeerror' => null
            );
        }
        return $this->formatResponse($messageArray, $type);
    }

    public function validateData($data, $parId)
    {

        $childId = $data['childId'];
        $gradeId = $data['grade_level'];
        $childFirstName = trim(ucwords($data['firstname']));
        $childLastName = "";
        $cgpaAr = $data['cgpaValue'];
        $cgpaFraction = 0;
        if (isset($data['cgpaFraction']) && $data['cgpaFraction'] != '') {
            $cgpaFraction = $data['cgpaFraction'];
        }
        $schoolName = $data['schoolName'];
        $childGender = "";
        if (isset($data['sexOfChild']) && $data['sexOfChild'] != '') {
            $childGender = $data['sexOfChild']; // getting param childgender
        }
        $dateOfBirth = $data['dateOfBirth']; // getting param childgender

        $message = '';
        //validates childId
        if (empty($message) && !validateNotNull($childId)) {
            $message = "Child id can't be blank";
            $typeError = null;
        }
        // validates first name
        if (empty($message)) {
            $message = validateKidFirstName($childFirstName);
            $typeError = "name";
        }

        //validates lastName
        if (empty($message) && $message == null && !empty($childLastName)) {
            $message = validateKidLastName($childLastName);
            $typeError = "name";
        }
        // checking child name existane for the parent
        $res = $this->_checkChildName($childId, $childFirstName, $parId);
        if (empty($message) && $res == true) {
            $message = 'Child first name already exist';
            $typeError = "name";
        }
        //validate kid school name
        if (empty($message) && !empty($schoolName)) {
            if (validateMinLength($schoolName, '1') == false) {
                $message = "Please enter school name of min 1 characters";
                $typeError = "school";
            }
            if (empty($message) && validateMaxLength($schoolName, '255') == false) {
                $message = "Please enter school name of max 255 characters";
                $typeError = "school";
            }
        }

        //validate kid DOB
        if (empty($message) && !empty($dateOfBirth)) {
            // if for validate weekly Goal inside call function validateFieldWithRegex for match with Regexp
            if (empty($message) && !validateFieldWithRegex($dateOfBirth, '/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.]((?:19\d\d|20\d\d))$/')) {
                $message = "Format of date of birth should be mm/dd/yyyy";
                $typeError = "dob";
            }
            if (empty($message) && validateFutureDate($dateOfBirth, date("m/d/Y")) == false) {
                $message = "Date of birth should not be future date";
                $typeError = "dob";
            }
        }

        if (empty($message) && !validateNotNull($gradeId)) {
            $message = "Please select grade level";
            $typeError = "grade";
        } else if (empty($message) && validateDigits($gradeId) == false) {
            $message = "Only numbers are allowed in child grade";
            $typeError = "grade";
        } else if (empty($message) && $gradeId != null && !empty($gradeId) && ($gradeId < 0 || $gradeId > 12)) {
            $message = "Child grade should be between 1 to 12";
            $typeError = "grade";
        }

        //validates gender
        if (empty($message) && $message == null) {
            // if (validateNotNull($childGender) == false) {
            //      $message = 'Please select child gender';
            //      $typeError = "childgender";
            //  }
        }
        if (empty($message) && $cgpaAr == '0' && $cgpaFraction == '0') {
            $message = "Child's gpa should be between 1 to 10";
            $typeError = "cgpa";
        }


        if (empty($message) && (!empty($cgpaAr) || !empty($cgpaFraction))) {
            if ((validateDigits($cgpaAr) == false) || (validateDigits($cgpaFraction) == false)) {
                $message = "Only numbers are allowed in child gpa";
                $typeError = "cgpa";
            } else if (($cgpaAr < '0' || $cgpaAr > '10' || $cgpaFraction < '0' ||
                    $cgpaFraction > '9') || ($cgpaAr >= '10' && $cgpaFraction > '0') || ($cgpaAr === '0' && $cgpaFraction > '0')) {
                $message = "Child's gpa should be between 1 to 10";
                $typeError = "cgpa";
            }
        }
        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'status' => 'error',
                'childId' => null,
                'gradeChange' => null,
                'action' => null,
                'typeerror' => $typeError,
                'status_code' => '110012',
            );

            return $messageArray;
        }

        return FALSE;
    }

    private function _checkChildName($childId, $childName, $parId)
    {
        // check child name exist with parent or not
        $checkNameres = $this->_objectchild->checkChildNameWithParent($childId, strtolower($childName), $parId);
        return $checkNameres;
    }

    public function formatData($data, $parId, $type)
    {

        $date = todayZendDate();

        $childFirstName = trim($data['firstname']);
        $childLastName = "";
        $nickName = "";
        $childName = $childFirstName . ' ' . $childLastName;
        $sexOfChild = "";
        if (isset($data['sexOfChild']) && $data['sexOfChild'] != '') {

            $sexOfChild = $data['sexOfChild'];
        }
        $schoolName = trim(filterHtmlEntites($data['schoolName']));
        $gradeId = $data['grade_level'];

        $cgpaAr = $data['cgpaValue'];

        $cgpaFraction = 0;
        if (isset($data['cgpaFraction']) && $data['cgpaFraction'] != '') {
            $cgpaFraction = $data['cgpaFraction'];
        }



        // formates cgpa
        $cgpav = $cgpaAr;
        $cgpaF = $cgpaFraction;
        if ($cgpaF != '') {
            $cgpa = $cgpav . '.' . $cgpaF;
        } else {
            if ($cgpav == 10) {
                $cgpa = $cgpav . '.' . 0;
            } else {
                $cgpa = $cgpav;
            }
        }
        if ($type == 'web') {
            $imageFileName = $data['image_file_name']; // getting param imagesName
        } else {
            $imageFileName = '';
        }
        $dateOfBirth = !empty($data['dateOfBirth']) ? date('Y-m-d', strtotime($data['dateOfBirth'])) : NULL;

        $childBasicInfo = array(
            'name' => $childName,
            'firstname' => $childFirstName,
            'lastname' => $childLastName,
            'nickname' => $nickName,
            'dob' => $dateOfBirth,
            'gender' => $sexOfChild,
            'parent_id' => $parId,
            'school_name' => $schoolName,
            'gpa' => $cgpa,
            'grade_id' => $gradeId,
            'modified_date' => $date,
            'track_location' => !empty($data['track_location'])
        );
        if (!empty($imageFileName) && $imageFileName != null) {
            $childBasicInfo['image'] = $imageFileName;
        }

        return $childBasicInfo;
    }

    public function addLearningCustomisation($res, $gradeId)
    {
        if (!empty($res)) {
            $LCData = $this->_objectchild->getChildGoals($res);
            if (empty($LCData)) {
                $objLearningCustomisation = new Application_Service_Kid_Profile_LearningCustomisation();
                $objLearningCustomisation->addlearnigCustomization($res, null);
                $this->_objectchild->addChildSubject($res, $gradeId, null);
            }
        }
    }

    public function checkGradeChange($childId, $gradeId)
    {
        $tblChildgradePoint = new Application_Model_DbTable_ChildGradePoints();
        $checkGarade = $this->_objectchild->getCurrentGradeOfChildFromGradePoint($childId);
        if ($checkGarade[0]['grade_id'] != $gradeId) {
            $childPoints = $checkGarade[0]['points'];
            // adds child's total point as childs point in new grade

            $tblChildgradePoint->delete("child_id = $childId and grade_id = $gradeId");
            $this->_objectchild->insertGradePoint($childId, $gradeId, $childPoints);
            $this->_objectchild->addChildSubject($childId, $gradeId, null);

                //Here we have update this child gare so lets hit our leader board cron
                // current directory
                $leaderboardCronPath = dirname( getcwd() )."/scripts/leaderboardcron.php"; 
               shell_exec("php $leaderboardCronPath");
             
            return TRUE;
        }

        return FALSE;
    }

    public function formatResponse($response, $type)
    {

        if ($type == 'web') {
            unset($response['status_code']);
        } else {
            unset($response['status']);
        }
        return $response;
    }

}
