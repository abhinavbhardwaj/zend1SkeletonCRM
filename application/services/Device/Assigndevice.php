<?php

/**
 * PHP version 5
 * 
 * @category  Service_Kid_Profile_Apps
 * @package   Assign Device
 * @author    Ashwini Asgarwal <ashwini.agarwal@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_Device_Assigndevice
{

    /**
     * defined all object variables that are used in entire class
     */
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
        // creates object of class child
        $this->_objectchild = new Application_Model_Child();
        $this->_objectApps = new Application_Model_Apps ();
    }

    public function assigndevice($data)
    {
        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $tblChildweeklyGoal = new Application_Model_DbTable_ChildWeeklyGoal();
        $tblChildGoal = new Application_Model_DbTable_ChildGoals();
        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo();
        $objChild = new Application_Model_Child();
        $deviceKey = $data['device_key']; // getting param device_key
        $accessToken = $data['access_token']; // getting param device_key
        $childId = $data['childId']; // getting param device_key

        if ($childId == null || empty($childId)) {
            $messageArray = array(
                'message' => "Child id can't be empty",
                'status_code' => STATUS_ERROR
            );
            return $messageArray;
        }
        $objAuth = new Application_Service_User_AuthDevice();
        $validate = $objAuth->authenticate($deviceKey, $accessToken, null);
        if ($validate['status_code'] == STATUS_ERROR) {
            return $validate;
        }
      
        $deviceId = $validate['deviceId'];
        $parId = $validate['parentId'];
        $appObj = new Application_Service_Kid_Profile_Apps();
        $tblChldDevRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
        $childAss = $tblChldDevRel->associateDeviceWithChild($childId, $deviceId);
        $childweeklyGoal = $tblChildweeklyGoal->getWeeklyData("child_id = $childId");
        if (count($childweeklyGoal) == 0) {
            $childGoal = $tblChildGoal->getChildGoals($childId);
            $weeklyGoal = $childGoal['weekly_points'];
            $addWeeklyGoal = $objChild->addWeeklyGoalForChild($childId, $parId, $weeklyGoal);
        }
        if ($childAss) {
            $childInfo = $tblChildInfo->fetchRow("child_id = $childId");
            $childName = $childInfo['name'];
            $parentInfo = $tblParentInfo->fetchRow("parent_id = $parId");
            $userId = $parentInfo['user_id'];
            $deviceInfo = $tblDeviceInfo->fetchRow("device_id = $deviceId");
            $deviceName = $deviceInfo['device_name'];
            $insertNotifdata = array(
                'user_id' => $userId,
                'notification_type' => 'TROPHY',
                'description' => "is associated with " . $deviceName,
                'seen_by_user' => 'N',
                'deleted' => 'N',
                'child_device_id' => $deviceId,
                'childe_name' => $childName,
                'child_id' => $childId,
                'created_date' => date('Y-m-d H:i:s')
            );

            // check if notification is to be sent
            if(empty($data['send_notification']) || $data['send_notification'] != 'false' ){
                $resnotifis = $tblParentNofic->AddParentNotification($insertNotifdata);
            }            
            $appsData = $appObj->getapps($childId, $deviceId);
            //if childerns info array is not empty
            if (!empty($appsData) && $appsData != null) {
                $newarray['status_code'] = STATUS_SUCCESS;
                $newarray['message'] = "successfull";
                $newarray['app_list'] = $appsData;
            } else { //ekse empty than 
                $newarray['status_code'] = STATUS_SYSTEM_ERROR;
                $newarray['message'] = "No app is available";
                $newarray['app_list'] = null;
            }
        } else { //ekse empty than 
            $newarray['status_code'] = STATUS_SYSTEM_ERROR;
            $newarray['message'] = "Some error while associating with child";
            $newarray['app_list'] = null;
        }
        return $newarray;
    }

}
