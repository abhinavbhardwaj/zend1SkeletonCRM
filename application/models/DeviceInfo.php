<?php

/*
 * This is a model class To Use Device related Classes of DBTable
 * Created By Suman Khatri
 * October 08 2014
 */

class Application_Model_DeviceInfo extends Zend_Loader_Autoloader 
{
    /*
     * defined all object variables that are used in entire class
     */

    private $_deviceInfo;
    private $_parentDeviceInfo;
    private $_childDeviceInfo;
    private $_objectchild;
    private $_objectParent;

    /*
     * function for create all model table object used this object to call model 
     * table functions
     */

    public function __construct() 
    {
        //creates object for model file DeviceInfo
        $this->_deviceInfo = new Application_Model_DbTable_DeviceInfo ();
        //creates object for model file ParentDeviceRelationInfo
        $this->_parentDeviceInfo = new 
            Application_Model_DbTable_ParentDeviceRelationInfo();
        //creates object for model file ChildDeviceRelationInfo
        $this->_childDeviceInfo = new 
            Application_Model_DbTable_ChildDeviceRelationInfo();
        //creates object of class child
        $this->_objectchild = new Application_Model_Child();
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
    }

    /**
     * @desc Function to add device info
     * @param array $data
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function addDeviceInfo($insertData) 
    {
        //add device data 
        $deviceId = $this->_deviceInfo->addDeviceInfo($insertData); 
        return $deviceId; //returns last insert Id
    }

    /**
     * @desc Function to update device info
     * @param array $data,$deviceId
     * @author suman khatri on October 08 2014
     * @return $result
     */
    public function updateDeviceInfo($updateData, $deviceId) 
    {
        //add device data 
        $result = $this->_deviceInfo->updateDeviceInfo($updateData, $deviceId); 
        return $result; //returns $result
    }

    /**
     * @desc Function to check device is already exist or not in bal_device_info
     * @param $deviceKey
     * @author suman khatri on October 08 2014
     * @return result
     */
    public function checkDeviceExistOrNotInDeviceInfo($deviceKey) 
    {
        //get device data if exist
        $deviceInfo = $this->_deviceInfo
            ->checkDeviceExistOrNotInDeviceInfo($deviceKey); 
        if (!empty($deviceInfo) && $deviceInfo != null) {
            //returns device_id if device key is exist
            return $deviceInfo['device_id']; 
        } else {
            return false; //returns false if device key is not exist
        }
    }

    /**
     * @desc Function to check device is already exist or not in 
     * bal_parent_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return array or null
     */
    public function checkDeviceExistOrNotInParentDeviceRelation($deviceId) 
    {
        //get device data if exist
        $deviceInfo = $this->_parentDeviceInfo
            ->checkDeviceExistOrNotInParentDeviceRelation($deviceId); 
        return $deviceInfo; //returns result
    }

    /**
     * @desc Function to check device is already exist or not in bal_child_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return array or null
     */
    public function checkDeviceExistOrNotInChildDeviceRelation($deviceId,$childId) 
    {
        //get device data if exist
        $deviceInfo = $this->_childDeviceInfo
            ->checkDeviceExistOrNotInChildDeviceRelation($deviceId,$childId); 
        return $deviceInfo; //returns result
    }

    /**
     * @desc Function to delete record from bal_parent_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function deleteParentDeviceRecord($id) 
    {
        $result = $this->_parentDeviceInfo->deleteData($id); //delete record
        return $result; //returns result
    }

    /**
     * @desc Function to delete record from bal_child_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function deleteChildDeviceRecord($id) 
    {
        $result = $this->_childDeviceInfo->unAssociatePhone($id); //delete record
        return $result; //returns result
    }

    /**
     * @desc Function to add parent device info
     * @param array $data
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function addParentDeviceInfo($insertData) 
    {
        //add parent device data
        $inserId = $this->_parentDeviceInfo->addParentDeviceInfo($insertData);  
        return $inserId; //returns last insert Id
    }

    /**
     * 
     * @param array $data
     * @return int
     */
    public function addOrUpdateDeviceData($data)
    {
        $deviceData = array(
            'device_name' => !empty($data['deviceName']) ? $data['deviceName'] : '',
            'longitude' => !empty($data['longitude']) ? $data['longitude'] : '',
            'latitude' => !empty($data['latitude']) ? $data['latitude'] : '',
            'device_key' => !empty($data['device_key']) ? $data['device_key'] : '',
            'registered_id' => !empty($data['regID']) ? $data['regID'] : '',
            'device_type' => !empty($data['device_type']) ? $data['device_type'] : '',
            'os_type' => !empty($data['os_type']) ? $data['os_type'] : '',
            'version' => !empty($data['version']) ? $data['version'] : '',
            'manufacturer' => !empty($data['manufacturer']) ? $data['manufacturer'] : '',
            'modified_date' => date('Y-m-d H:i:s'),
            'device_removed' => 'N',
            'device_monitored' => 'Y',
            'device_lock_status' => 'UNLOCK',
        );

        //check if device is exist in bal_device_info table or not
        $deviceId = $this->checkDeviceExistOrNotInDeviceInfo($deviceData['device_key']);
        if (!empty($deviceId)) {

            //check if device is exist in bal_parent_device_relation table or not
            $deviceInParentDeviceRelation = $this->checkDeviceExistOrNotInParentDeviceRelation($deviceId);
            if (!empty($deviceInParentDeviceRelation)) {
                $this->deleteParentDeviceRecord($deviceInParentDeviceRelation['id']);
            }

            //check if device is exist in bal_child_device_relation table or not
            $deviceInChildDeviceRelation = $this->checkDeviceExistOrNotInChildDeviceRelation($deviceId, null);
            if (!empty($deviceInChildDeviceRelation)) {
                $this->deleteChildDeviceRecord($deviceInChildDeviceRelation['id']);
            }

            //update device info into DB
            $this->updateDeviceInfo($deviceData, $deviceId);
        } else {
            //add device info into DB
            $deviceId = $this->addDeviceInfo($deviceData);
        }

        return $deviceId;
    }

    /**
     * @desc Function to pair device with parent
     * @param $deviceId,$parentId,$accessToken
     * @author suman khatri on October 08 2014
     * @return id
     */
    public function pairDeviceWithParent($deviceId, $parentId, $accessToken) 
    {
        //if device id not empty
        if (!empty($deviceId) && $deviceId != null) {
            //create array to add device and parent data in 
            //bal_parent_device_relation
            $addParentDeviceData = array('parent_id' => $parentId,
                'device_id' => $deviceId,
                'access_token' => $accessToken);
            //add device and parent data in bal_parent_device_relation
            $parentDeviceId = $this->_parentDeviceInfo->addParentDeviceInfo(
                $addParentDeviceData
            );
            if (!empty($parentDeviceId) && $parentDeviceId != null) {
                return $parentDeviceId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @desc Function to pair device with child
     * @param $deviceId,$childId
     * @author suman khatri on October 09 2014
     * @return id
     */
    public function pairDeviceWithChild($deviceId, $childId) 
    {
        //if device id not empty
        if ((!empty($deviceId) && $deviceId != null) 
            && (!empty($childId) && $childId != null)) {
            //create array to add device and parent data in 
            //bal_parent_device_relation
            $addChildDeviceData = array(
                'child_id' => $childId,
                'device_id' => $deviceId,
                'date_association' => todayZendDate());
            //add device and parent data in bal_parent_device_relation
            $childDeviceId = $this->_childDeviceInfo
                ->addChildDeviceInfo($addChildDeviceData);
            if (!empty($childDeviceId) && $childDeviceId != null) {
                return $childDeviceId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @desc Function to verify device_key and accessToken
     * @param $deviceKey,$accessToken
     * @author suman khatri on October 09 2014
     * @return ArrayObject/parentId
     */
    public function verifyDeviceKeyAndAccessToken($deviceKey, $accessToken) 
    {
        //check if device is exist in bal_device_info table or not
         //get device data if exist
        $deviceInfo = $this->_deviceInfo->checkDeviceExistOrNotInDeviceInfo(
            $deviceKey
        );
        //if device id is exist
        if (!empty($deviceInfo) && $deviceInfo != null) {
            $deviceId = $deviceInfo['device_id'];
            //get parentId from accesstoken and deviceKey
            $parentId = $this->_objectParent
                ->getParentIdUsingAccessTokenAndDeviceId($accessToken, $deviceId
                    , 'parId'
                );
            //if parentId is emapty
            if (($parentId != '' || $parentId != null) || $parentId == 0) {
                //returns message array
                $status = array(
                    'message' => null,
                    'parentId' => $parentId,
                    'deviceId' => $deviceId
                );
                return $status; 
            } else {
                //returns message array
                $status = array(
                    'message' => 'Invalid device key and access token',
                    'parentId' => null
                );
                return $status;
            }
        } else { //if device id is not exist
            //returns message array
            $status = array(
                'message' => 'Invalid device key',
                'parentId' => null
            );
            return $status;
        }
    }
    
    /**
     *@desc Function to get child devices
     *@param array $childId,$parId
     * 
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildDevices($childId, $parId = NULL) 
    {
        //add parent device data
        $inserId = $this->_childDeviceInfo->getChildDevices($childId, $parId);  
        return $inserId; //returns last insert Id
    }

}
