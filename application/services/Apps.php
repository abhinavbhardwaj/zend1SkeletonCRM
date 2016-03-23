<?php

/**
 * Manage apps from android app
 * 
 * @author Ashwini Agarwal
 */
class Application_Service_Apps
{

    /**
     * Device Id
     */
    protected $_deviceId;

    /**
     * Parent Id
     */
    protected $_parentId;

    /**
     * Child Id
     */
    protected $_childId;

    /**
     *
     * @var Application_Model_Apps
     */
    protected $_dbApps;

    /**
     * Notification Data
     */
    protected $_notificationData = null;

    /**
     * Child List
     */
    protected $_childList = null;

    /**
     * Validate parent and
     * throw exception if user is not valid
     * 
     * @param $deviceKey
     * @param $accessToken
     * @param $childId
     * @throws Exception
     */
    public function __construct($deviceKey, $accessToken, $childId = NULL)
    {
        $objAuth = new Application_Service_User_AuthDevice();
        $validate = $objAuth->authenticate($deviceKey, $accessToken, $childId);

        if ($validate['status_code'] == STATUS_ERROR) {
            throw new Exception($validate['message']);
        }

        $this->_dbApps = new Application_Model_Apps();
        $this->_deviceId = $validate['deviceId'];
        $this->_parentId = $validate['parentId'];
        $this->_childId = $childId;
    }

    /**
     * Update app type to productive or unproductive
     * 
     * @param Array $appData
     * @return Array
     */
    public function updateType($appData)
    {
        foreach ($appData as $app) {
            if ($app->is_prod == 'N') {
                $this->_dbApps->updateAppToProductive($app->appId);
            } elseif ($app->is_prod == 'Y') {
                $this->_dbApps->updateAppToUnProductive($app->appId);
            }
        }

        return array('message' => "successful", 'status_code' => '110011');
    }

    /**
     * Delete all application from device
     * Generally done before when device first setup
     */
    public function reset()
    {
        $this->_dbApps->removeDeviceAppsForParentAndDevice($this->_deviceId);
        $this->_dbApps->removeDeviceAppsForChildAndDevice($this->_deviceId);
        return array('message' => "success", 'status_code' => '110011');
    }

    /**
     * Add multiple apps to database
     * 
     * @param Array $apps
     * @return Array
     */
    public function addArray($apps)
    {
        $return = array();
        foreach ($apps as $app) {
            $return['app_status'][] = $this->add($app->app_name, $app->package_name, $app->images);
        }

        $return['app_list'] = NULL;
        if (!empty($this->_childId)) {
            $appObj = new Application_Service_Kid_Profile_Apps();
            $return['app_list'] = $appObj->getapps($this->_childId, $this->_deviceId);
        }

        $return["status_code"] = STATUS_SUCCESS;
        $return["message"] = "Request processed";
        return $return;
    }

    /**
     * Add app to database
     * 
     * @param String $appName
     * @param String $packageName
     * @param String $image
     * @return Array
     */
    public function add($appName, $packageName, $image)
    {
        if ($this->_dbApps->isAlreadyExists($this->_parentId, $this->_deviceId, $packageName)) {
            return array('status_code' => STATUS_ERROR, 'message' => 'App already exist');
        }

        $imageTitle = $this->uploadAppImage($image, $appName);
        $this->addToDatabase($appName, $packageName, $imageTitle);

        if (!empty($this->_childId)) {
            $this->notifyParent($appName);
        }

        return array('status_code' => STATUS_SUCCESS, 'message' => 'App added successfully');
    }

    /**
     * Save image data to server and return file name
     * 
     * @param string $image
     * @param string $appName
     * @return string
     */
    private function uploadAppImage($image, $appName)
    {
        $fileName = preg_replace("/[^A-Za-z0-9]/", '', $appName) . '_' . time() . '.png';
        $s3 = new My_Service_Amazon_S3();
        $s3->save(My_Thumbnail::getThumbnail(base64_decode($image), 'png', 72, 72), 'apps/' . $fileName);
        return $fileName;
    }

    /**
     * Perform final database operations to save app to DB
     * 
     * @param string $appName
     * @param string $packageName
     * @param string $imageTitle
     */
    private function addToDatabase($appName, $packageName, $imageTitle)
    {
        $this->_dbApps->insertParentApp($this->_deviceId, $this->_parentId, $appName, $packageName, $imageTitle);

        $dbDefaultApp = new Application_Model_DbTable_DeviceDefaultApps();
        $status = $dbDefaultApp->isProductiveApp($packageName) ? 'N' : 'Y';

        foreach ($this->getChildList() as $child) {
            $childId = $child['child_id'];
            $this->_dbApps->insertChildApp($this->_deviceId, $childId, $appName, $packageName, $imageTitle, $status);
        }
    }

    /**
     * Add notification for current user
     * 
     * @param String $app_name
     * @param String $action
     */
    private function notifyParent($app_name, $action = 'INSTALL')
    {
        $data = $this->getNotificationData($action);
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['description'] = sprintf($data['description'], $app_name);

        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblParentNofic->AddParentNotification($data);
    }

    /**
     * Get notification data to be insert into database
     * 
     * @return Array
     */
    private function getNotificationData($action)
    {
        $isInstall = $action == 'INSTALL';
        if (empty($this->_notificationData)) {

            $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo();
            $deviceInfo = $tblDeviceInfo->fetchRow(array("device_id = ?" => $this->_deviceId));

            $tblParentInfo = new Application_Model_DbTable_ParentInfo();
            $parentInfo = $tblParentInfo->fetchRow(array('parent_id = ?' => $this->_parentId));

            $tblChildInfo = new Application_Model_DbTable_ChildInfo();
            $childInfo = $tblChildInfo->fetchRow(array('child_id = ?' => $this->_childId));

            $this->_notificationData = array(
                'user_id' => $parentInfo['user_id'],
                'notification_type' => $isInstall ? 'APPIN' : 'APPUN',
                'description' => ($isInstall ? "installed %s in " : "uninstalled %s from ") . $deviceInfo['device_name'],
                'child_device_id' => $this->_deviceId,
                'childe_name' => $childInfo['name'],
                'child_id' => $this->_childId
            );
        }

        return $this->_notificationData;
    }

    /**
     * Remove an installed application
     * 
     * @param Array $data
     * @return Array
     */
    public function remove($data)
    {
        if (empty($data['app_name']) || empty($data['package_name'])) {
            return array('status_code' => STATUS_ERROR, 'message' => 'Fields can not be blank');
        }

        $this->removeFromDatabase($data['package_name']);

        if (!empty($this->_childId)) {
            $this->notifyParent($data['app_name'], 'REMOVE');
        }

        return array('status_code' => STATUS_SUCCESS, 'message' => 'App removed successfully');
    }

    /**
     * Perform final database operations to remove app from DB
     * 
     * @param string $package
     */
    private function removeFromDatabase($package)
    {
        $tblParentDeviceApps = new Application_Model_DbTable_ParentDeviceApps();
        $tblParentDeviceApps->delete(array(
            "device_id = ?" => $this->_deviceId,
            "package_name = ?" => $package
        ));

        $dbKidDeviceApp = new Application_Model_DbTable_DeviceApps();
        foreach ($this->getChildList() as $child) {
            $childId = $child['child_id'];
            $dbKidDeviceApp->delete(array(
                "child_device_id = ?" => $this->_deviceId,
                "package_name = ?" => $package,
                "child_id = ?" => $childId
            ));
        }
    }

    /**
     * get list of kids
     * 
     * @return array
     */
    private function getChildList()
    {
        if ($this->_childList == NULL) {
            $dbKid = new Application_Model_DbTable_ChildInfo();
            $this->_childList = $dbKid->getChildbasicinfo($this->_parentId);
        }

        return $this->_childList;
    }

    /**
     * Unadmin Finny App
     * 
     * @return Array
     */
    public function unadminFinny()
    {
        if (empty($this->_childId)) {
            return array('status_code' => STATUS_ERROR, 'message' => 'child id cannot be empty');
        }
        $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo();
        $deviceInfo = $tblDeviceInfo->fetchRow(array("device_id = ?" => $this->_deviceId));

        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $parentInfo = $tblParentInfo->fetchRow(array('parent_id = ?' => $this->_parentId));

        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $childInfo = $tblChildInfo->fetchRow(array('child_id = ?' => $this->_childId));

        $data = array(
            'user_id' => $parentInfo['user_id'],
            'notification_type' => 'APPADMRM',
            'description' => "deactivated 'finny' from " . $deviceInfo['device_name'] . ". Your child can remove 'finny' anytime!",
            'child_device_id' => $this->_deviceId,
            'childe_name' => $childInfo['name'],
            'child_id' => $this->_childId,
            'created_date' => date('Y-m-d H:i:s')
        );

        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblParentNofic->AddParentNotification($data);

        return array('status_code' => STATUS_SUCCESS, 'message' => 'Application is removed from admin app successfully');
    }

    public function updateFinnyVersion($data)
    {
        if (empty($this->_childId)) {
            return array('status_code' => STATUS_ERROR, 'message' => 'child id cannot be empty');
        }

        if (empty($data['version'])) {
            return array('status_code' => STATUS_ERROR, 'message' => 'version cannot be empty');
        }

        $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo();
        $deviceInfo = $tblDeviceInfo->fetchRow(array("device_id = ?" => $this->_deviceId));

        if ($deviceInfo->version == $data['version']) {
            return array('status_code' => STATUS_ERROR, 'message' => 'version is same');
        }

        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $parentInfo = $tblParentInfo->fetchRow(array('parent_id = ?' => $this->_parentId));

        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $childInfo = $tblChildInfo->fetchRow(array('child_id = ?' => $this->_childId));

        $updateData = array(
            'version' => $data['version'],
            'modified_date' => date('Y-m-d H:i:s')
        );
        $tblDeviceInfo->updateDeviceInfo($updateData, $this->_deviceId);

        $gender = $childInfo->gender == 'B' ? 'his' : ($childInfo->gender == 'G' ? 'her' : '');
        $insert = array(
            'user_id' => $parentInfo['user_id'],
            'notification_type' => 'VERSION',
            'description' => "has upated to the latest verison (" . $data['version'] . ") of Finny in " . $gender . " " . $deviceInfo->device_name,
            'child_device_id' => $this->_deviceId,
            'childe_name' => $childInfo['name'],
            'child_id' => $this->_childId,
            'created_date' => date('Y-m-d H:i:s')
        );

        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblParentNofic->AddParentNotification($insert);

        return array('status_code' => STATUS_SUCCESS, 'message' => 'version updated succesfully');
    }

}
