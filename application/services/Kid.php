<?php

/**
 * PHP version 5
 * 
 * @category  Service_Kid
 * @package   Kid
 * @author    Suman Khatri <suman.khatri@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_Kid
{

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectParent;
    private $_objectDeviceInfo;
    private $_objectchild;
    private $_objectApps;

    /**
     * construct funtion
     */
    public function __construct()
    {
        //including functions.php
        include_once APPLICATION_PATH . '/../library/functions.php';
        //including validate.php
        include_once APPLICATION_PATH . '/../library/validate.php';
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
        // creates object of class DeviceInfo
        $this->_objectDeviceInfo = new Application_Model_DeviceInfo();
        // creates object of class child
        $this->_objectchild = new Application_Model_Child();
        //creates object of class app
        $this->_objectApps = new Application_Model_Apps();
    }

    /**
     * @desc Function to save kid info
     * @param array $data data to be send 
     * @param int   $type type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     */
    public function savekidinfo(array $data, $type)
    {
        $isMobile = $type == 'mobile';
        if (($validate = $this->_validateKidData($data, $isMobile)) != NULL) {
            return $validate;
        }

        // if request from web, 
        // it cannot be virtual kid
        $parentId = 0;
        $deviceId = '';
        $isVirtualKid = $isMobile;
        if ($isMobile && !empty($data['access_token'])) {
            $objAuth = new Application_Service_User_AuthDevice();
            $validateAuth = $objAuth->authenticate($data['device_key'], $data['access_token']);
            if ($validateAuth['status_code'] == STATUS_ERROR) {
                return $validateAuth;
            }

            $isVirtualKid = FALSE;
            $parentId = $validateAuth['parentId'];
            $deviceId = $validateAuth['deviceId'];
        }

        if (!$isMobile) {
            $userLoginInfo = Zend_Auth::getInstance()->getIdentity();
            $parentId = $this->_objectParent->getParentData($userLoginInfo->user_id, 'parId');
        }

        $kidNameKey = $isMobile ? 'kid_f_name' : 'KidFirstName';
        $gradeKey = $isMobile ? 'grade' : 'grade_level';
        $kidFname = $data[$kidNameKey];
        $gradeId = $data[$gradeKey];

        if (!$isVirtualKid && $this->_objectchild->checkChildNameWithParent(null, $kidFname, $parentId)) {
            return array(
                'message' => 'Child first name already exist',
                'status_code' => STATUS_ERROR,
            );
        }
        if(empty($data['dateOfBirth']))
        $data['dateOfBirth'] = date("Y-m-d"); //we are not going to save child's age if he is below 13 so if we don't have age then lets say Today is child birthday 
        $birth      =       new Zend_Date($data['dateOfBirth']);    
        $today      =       new Zend_Date();
        $diff       =       $today->sub($birth)->toValue();
        $age        =       floor($diff / 3600 / 24 / 365);

        
       if($age < 13){
            $data['coppa_required']     =       1;
            $dateOfBirth                =       NULL; 
       }
       else{
            $data['coppa_required']     =       0;
             if (!$isMobile) 
                $dateOfBirth                =        date("Y-m-d", strtotime($data['dateOfBirth']));  
             else
                $dateOfBirth                =       date("Y-m-d", strtotime(str_replace('/', '-',$data['dateOfBirth'])));        
       }

       
        if (isset($data['coppa_required'])) {
            $coppa_required = $data['coppa_required'];
        }
         
        $childId = $this->addChildAndInitiateData($kidFname, $gradeId, $parentId, $coppa_required, $dateOfBirth);
        $childInfoArray = $this->_objectchild->getChildInfoArray($childId);
 
        if ($isVirtualKid) {
            $deviceId = $this->_objectDeviceInfo->addOrUpdateDeviceData($data);
            // Generate the code for verrification of parent email
            $accessToken = substr(rand(), 0, 9);
            $this->_objectDeviceInfo->pairDeviceWithParent($deviceId, $parentId, $accessToken);

            //pairing device with child
            $childDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
            $childDeviceRel->associateDeviceWithChild($childId, $deviceId);

            $objApps = new Application_Service_Apps($data['device_key'], $accessToken);
            $objApps->reset();
        } else {

            if ($coppa_required) {
                $this->_objectchild->resetCoppaReminder($childId);
                $objCoppa = new Application_Service_Coppa($parentId, $childId);
                $objCoppa->send();
            }

                $this->assignParentAppsToKid($parentId, $childId, null);
                $this->_objectchild->sendPushOnAddOrUpdateKid($parentId, $childInfoArray, 'add', $childId, $deviceId);

            if ($isMobile) {
                $accessToken = $data['access_token'];
            }
        }

        if (!$isMobile) {
            return array(
               // 'message' => "Child added successfully" . ($coppa_required ? " and email is sent to your email id to accept the 'COPPA' consent." : '.'),
                'message'           =>  ($coppa_required ? "Please check your email ".$userLoginInfo->email." and provide COPPA Consent." : 'Child added successfully.'),
                'coppa_required'    =>  $coppa_required,
                'status' => 'success',
                'child_id' => $childId
            );
        }

        return array(
            'message' => 'successfull',
            'status_code' => STATUS_SUCCESS,
            'children_list' => $childInfoArray,
            'access_token' => !empty($accessToken) ? $accessToken : NULL
        );
    }

    /**
     * @desc Function to validate kid values
     * @author suman khatri on October 08 2014
     * 
     * @param array $data
     * @param type $isMobile
     * @return array
     */
    private function _validateKidData(array $data, $isMobile)
    {
        $kidNameKey = $isMobile ? 'kid_f_name' : 'KidFirstName';
        $kidFname = !empty($data[$kidNameKey]) ? $data[$kidNameKey] : '';
        $message = validateKidFirstName($kidFname);
        if (!empty($message)) {
            return array('message' => $message, 'status' => 'error', 'status_code' => STATUS_ERROR);
        }

        $gradeKey = $isMobile ? 'grade' : 'grade_level';
        if (empty($data[$gradeKey]) || !validateGrade($data[$gradeKey])) {
            return array('message' => 'grade is not valid or empty', 'status' => 'error', 'status_code' => STATUS_ERROR);
        }

        if ($isMobile) {
            if (empty($data['device_key'])) {
                return array('message' => "Device key can't be null or empty", 'status' => 'error', 'status_code' => STATUS_ERROR);
            }
            if (empty($data['access_token']) && empty($data['deviceName'])) {
                return array('message' => "Device name can't be null or empty", 'status' => 'error', 'status_code' => STATUS_ERROR);
            }
        }

        return NULL;
    }

    /**
     * @desc Function to get all child info 
     * @param array  $data data to be valiate
     * @param string $type type of request
     * 
     * @author suman khatri on October 09 2014
     * @return array
     */
    public function getKidsList(array $data)
    { 
        $deviceKey = $data['device_key']; // getting param device_key
        $accessToken = $data['access_token']; // getting param device_key
        $objAuth = new Application_Service_User_AuthDevice();
        $validate = $objAuth->authenticate($deviceKey, $accessToken, null);
        if ($validate['status_code'] == STATUS_ERROR) {
            return $validate;
        }
        $parentId = $validate['parentId']; 
        //getting childLidt
        $childrensList = $this->_objectchild->getChildrensAllInfoUsingParentIdOrChildId($parentId, null);

        //formatting child array list
        $childerArray = $this->_objectchild->formateChildArray($childrensList);

        //if childerns info array is not empty
        if (!empty($childrensList) && $childrensList != null) {
            $newarray['status_code'] = STATUS_SUCCESS;
            $newarray['message'] = "successfull";
            $newarray['children_list'] = $childerArray;
            return $newarray;
        } else {
            $newarray['status_code'] = STATUS_SYSTEM_ERROR;
            $newarray['message'] = "some error occurs while getting children list";
            $newarray['children_list'] = null;
            return $newarray;
        }
    }

    public function assignParentAppsToKid($parentId, $kidId, $deviceId = NULL)
    {
        $dbParentDeviceApp = new Application_Model_DbTable_ParentDeviceApps();
        $apps = $dbParentDeviceApp->getParentDeviceApps($parentId, $deviceId);
        foreach ($apps as $app) {
            $this->_objectApps->insertChildApp($app->device_id, $kidId, $app->app_name, $app->package_name, $app->app_image, 'Y');
        }
    }

    public function addChildAndInitiateData($name, $grade, $parentId, $coppa_required, $dateOfBirth=null)
    {
        $dateOfBirth    =   (isset($dateOfBirth)) ? $dateOfBirth : null;
        $childId        =   $this->_objectchild->addOrUpdateKidsBasicInfo($name, null, $dateOfBirth, null, $grade, null, null, $parentId, $coppa_required);
        $this->_objectchild->insertGradePoint($childId, $grade, null);
        //adding child's goal info
        $LCData = $this->_objectchild->getChildGoals($childId);
        if (empty($LCData)) {
            //add learning customization
            $this->_objectchild->addlearnigCustomization($childId, null);
            //add subject
            $this->_objectchild->addChildSubject($childId, $grade, null);
        }

        return $childId;
    }
    
    /**
     * @desc Function to delete child
     * @param int  childId
     * @author Abhinav Bhardwaj on January 02 2016
     * @return int responce
     */
    public function deleteKid($childId, $type){
        if($type=='web'){  
          $responce         =   $this->_objectchild->deleteChild($childId);
          return $responce;
        }  
    }

}
