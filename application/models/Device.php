<?php

class Application_Model_Device extends Zend_Loader_Autoloader {
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblDeviceInfo;
    private $_tblParentInfo;
    private $_tblDeviceUsage;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        //creates object for model file ChildDeviceInfo
        $this->_tblDeviceInfo = new Application_Model_DbTable_ChildDeviceInfo ();
        //creates object for model file ParentInfo
        $this->_tblParentInfo = new Application_Model_DbTable_ParentInfo();
        //creates object for model file deviceusage
        $this->_tblDeviceUsage = new Application_Model_DbTable_DeviceUsage();
    }

    /**
     * @desc Function to get device info using deviceIs
     * @param $childDeviceid
     * @author suman khatri on 18th November 2013
     * @return ArrayObject
     */
    public function getDeviceInfo($childDeviceid) {
        $deviceInfo = $this->_tblDeviceInfo->getChildDeviceData($childDeviceid); //fetches device data 
        return $deviceInfo; //returns array
    }

    /**
     * @desc Function to get all device list if child
     * @param $childId
     * @author suman khatri on 19th November 2013
     * @return ArrayIterator
     */
    public function getAllDevicesOfChild($childId) {
        $deviceList = $this->_tblDeviceInfo->getChildAllDevice($childId); //fetches all devices
        return $deviceList; //returns array
    }

    public function getChildWithAllDevice($childId) {
        $deviceList = $this->_tblDeviceInfo->getChildWithAllDevice($childId); //fetches all devices
        return $deviceList; //returns array
    }

    /**
     * @desc Function to update device data of child
     * @param $deviceId,$action,$newStatus
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function updateDeviceData($deviceId, $action, $newStatus) {
        if ($action == 'removeDevice') {
            $updatePhoneData = array(
                'device_removed' => 'Y',
                'modified_date' => todayZendDate()
            );
        }
        if ($action == 'deviceMoniter') {
            $updatePhoneData = array(
                'device_monitored' => $newStatus,
                'modified_date' => todayZendDate()
            );
        }
        if ($action == 'updateDeviceInfo') {
            $updatePhoneData = $newStatus;
        }
        //updates data into DB
        $result = $this->_tblDeviceInfo->UpdateDevicePhoneData($updatePhoneData, $deviceId);
        return $result; //returns result
    }

    /**
     * @desc Function to check device name is exist with same child or not 
     * @param $deviceName, $childId ,$action, $deviceId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function checkDeviceNameWithChild($deviceName, $childId, $action, $deviceId) {
        //checking device name is exist with same child or not 
        $result = $this->_tblDeviceInfo->CheckMobileAlreadyRegisterOrNot($deviceName, $childId, $action, $deviceId);
        return $result; //returns result
    }

    /**
     * @desc Function to check device phone number is exist with parent or not
     * @param $phoneNumber,$parentId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function checkPhoneNumebrExistWithParentRecord($phoneNumber, $parentId) {
        //checking whether phone number is exist or not in DB with parent's number
        if (!empty($parentId) && $parentId != null) {
            $phoneNumberRecord = $this->_tblParentInfo->checkPhoneExistOrnotonedit($phoneNumber, $parentId);
        } else {
            $phoneNumberRecord = $this->_tblParentInfo->checkPhoneExistOrnot($phoneNumber);
        }

        return $phoneNumberRecord; //returns result
    }

    /*     * *******function to check where parent safecheck PhoneNumebrWithSameParent ******* */

    function checkPhoneNumebrWithSameParent($phoneNumber, $parentId) {
        return $this->_tblParentInfo->checkPhoneExist($phoneNumber, $parentId);
    }

    /**
     * @desc Function to update device info
     * @param $unqueCodeGenerate,$date,$childPhoneNumber,$deviceName,$emailId, $deviceId,$childId,$uniqueCodeExp
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function updateDeviceInfo($unqueCodeGenerate, $date, $childPhoneNumber, $deviceName, $emailId, $deviceId, $childId, $uniqueCodeExp) {
        if (!empty($deviceId) && $deviceId != null) {
            $where = "child_device_id = $deviceId"; //where condition
            //updates device info
            $result = $this->_tblDeviceInfo->updateDeviceInfo($unqueCodeGenerate, $date, $childPhoneNumber, $deviceName, $emailId, $where);
        }
        if (!empty($childId) && $childId != null && !empty($uniqueCodeExp) && $uniqueCodeExp != null) {
            $where = "child_id= '$childId' AND unique_key = '$uniqueCodeExp'"; //where condition
            //updates device info
            $addDevice = $this->_tblDeviceInfo->updateDeviceInfo($unqueCodeGenerate, $date, $childPhoneNumber, $deviceName
                    , $emailId, $where);
        }
        return $result; //returns result
    }

    /**
     * @desc Function to check device is expired or not 
     * @param $childId ,$deviceName ,$uniqueCodeExp
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function checkExpiredDevice($childId, $deviceName, $uniqueCodeExp) {
        // check device is expired or not 
        $result = $this->_tblDeviceInfo->CheckExpiredDevice($childId, $deviceName, $uniqueCodeExp);
        return $result; //returns result
    }

    /**
     * @desc Function to get count of devices of child 
     * @param $childId
     * @author suman khatri on 19th November 2013
     * @return count
     */
    public function getCountOfchildDevices($childId) {
        //getting child devices
        $dataDevices = $this->_tblDeviceInfo->getChildDeviceId($childId);
        //getting count of records
        $count = count($dataDevices);
        return $count; //return count; 
    }

    /**
     * @desc Function to add device info into DB
     * @param $childId , $unqueCodeGenerate,$date,$childMobileNo,$deviceName,$emailId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function insertDeviceInfo($childId, $unqueCodeGenerate, $date, $childMobileNo, $deviceName, $emailId, $type) {
        //insert record into DB
        $result = $this->_tblDeviceInfo->insertDeviceInfo($childId, $unqueCodeGenerate, $date, $childMobileNo, $deviceName, $emailId, $type);
        return $result; //returns result
    }

    /**
     * @desc Function to get device info using $childId
     * @param $childId,$uniqueCode
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getDeviceInfoUsingChildIdandUniqueCode($childId, $uniqueCode) {
        $where = "child_id = '$childId' AND unique_key = '$uniqueCode'"; //where condition
        $phonenumber = $this->_tblDeviceInfo->fetchRow($where); //fetch device number and data
        return $phonenumber; //returns array
    }

    /**
     * @desc Function to get device key
     * @param $uniqueId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getDeviceKey($uniqueId) {
        $getDeviceId = $this->_tblDeviceInfo->getDeviceKey($uniqueId); //fetch data using uniqueId
        return $getDeviceId; //returns array
    }

    /**
     * @desc Function to check device name is exist with aonther record 
     * @param $deviceName
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function checkDeviceNameWithAnotherRecord($deviceName, $deviceId, $childId) {
        //checking device name is exist with another record 
        $result = $this->_tblDeviceInfo->CheckDeviceAlreadyRegister($deviceName, $deviceId, $childId);
        return $result; //returns result
    }

    /**
     * @desc Function to check phone number is exist or not
     * @param $deviceName
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function checkPhoneNumberWithAnotherRecord($phoneNumber, $childDeviceId) {
        //checking phone number is exist with another record 
        $result = $this->_tblDeviceInfo->CheckMobileAlreadyRegister($phoneNumber, $childDeviceId);
        return $result; //returns result
    }

    /*     * **********function public*********** */

    public function getDeviceIdByDeviceKey($deviceKey, $uniqueId) {
        return $this->_tblDeviceInfo->getDeviceIdDeviceKey($deviceKey, $uniqueId);
    }

    /**
     * @desc Function to get spent time on device for today
     * @param $deviceId,$childIdForInfo
     * @author suman khatri on 3rd December 2013
     * @return result
     */
    public function getDeviceUsageForTheDay($deviceId,$childId) {
        $data = $this->_tblDeviceUsage->getDeviceUsageForTheDay($deviceId,$childId); //fetches record
        return $data; //return array
    }
    
    /**
     * @desc Function to get spent time on device for today
     * @param $deviceId,$childIdForInfo
     * @return result
     */
    public function getDailyDeviceUsage($deviceId,$childId) {
        $data = $this->_tblDeviceUsage->getDailyDeviceUsage($deviceId,$childId); //fetches record
        return $data; //return array
    }
    
    

    /**
     * @desc Function to get spent time on device for week
     * @param $deviceId
     * @author suman khatri on 3rd December 2013
     * @return result
     */
    public function getDeviceUsageForWeek($deviceId, $startWeekDate, $endWeekDate,$childId) {
        if (date('w') == 0) {
            $firstDayOfWeek = date('Y-m-d');
        } else {
            $firstDayOfWeek = date('Y-m-d', strtotime('Last Sunday'));
        }

        $lastDayOfWeek = date('Y-m-d', strtotime('+6 days', strtotime($firstDayOfWeek)));
        if (!empty($startWeekDate) && !empty($endWeekDate)) {
            $firstDayOfWeek = date('Y-m-d', strtotime($startWeekDate));
            $lastDayOfWeek = date('Y-m-d', strtotime($endWeekDate));
        }

        $data = $this->_tblDeviceUsage->getDeviceUsageForWeek($deviceId, $firstDayOfWeek, $lastDayOfWeek,$childId); //fetches record
        $spentTime = $data['totalSpentTime'];
        return $spentTime; //return array
    }

    /**
     * @desc Function to get spent time on device for mobile stats
     * @param $deviceId
     * @author suman khatri on 5th December 2013
     * @return result
     */
     
     public function getDeviceUsageForMobileStatsLite($deviceId) {
		//getting spent time whole
		$spentTimeWhole = 0;
		$spentDataWhole = $this->_tblDeviceUsage->getTotalDeviceUsage($deviceId);
		$spentTimeWhole = $spentDataWhole['totalSpentTime'];
	}
     
    public function getDeviceUsageForMobileStats($deviceId,$childId) {
        //getting spent time whole
        $spentDataWhole = $this->_tblDeviceUsage->getTotalDeviceUsage($deviceId,$childId);
        $spentTimeWhole = $spentDataWhole['totalSpentTime'];

        //getting spent time for month
        $dataMonth = $this->_tblDeviceUsage->getDeviceUsageForMonth($deviceId,$childId); //fetches record for month
        $spentTimeMonth = $spentTimeMonth + $dataMonth['totalSpentTime'];
       
        //getting spent time for week
        if (date('w') == 0) {
            $firstDayOfWeek = date('Y-m-d');
        } else {
            $firstDayOfWeek = date('Y-m-d', strtotime('Last Sunday', time()));
        }
        $lastDayOfWeek = date('Y-m-d', strtotime('+6 days', strtotime($firstDayOfWeek)));

        $dataWeek = $this->_tblDeviceUsage->getDeviceUsageForWeek($deviceId, $firstDayOfWeek, $lastDayOfWeek,$childId); //fetches record
        $spentTimeWeek = $spentTimeWeek + $dataWeek['totalSpentTime'];

        //getting spent time for today
        $spentDataToday = $this->_tblDeviceUsage->getDeviceUsageForTheDay($deviceId,$childId);
        $spentTimeToday = $spentDataToday['duration'];
        
        $resultArray = array(
            'todaySpentTime' => $spentTimeToday,
            'weekSpentTime' => $spentTimeWeek,
            'monthSpentTime' => $spentTimeMonth,
            'totalSpentTime' => $spentTimeWhole
        );
        return $resultArray; //return array
    }

    /**
     * @desc Function to get all configured devices of child
     * @param $childId
     * @author suman khatri on 11th December 2013
     * @return ArrayIterator
     */
    public function getAllConfiguredDevicesOfChild($childId) {
        //getting all devices
        $allDevices = $this->_tblDeviceInfo->getChildAllDeviceForChild($childId);
        return $allDevices; //returns array
    }

    /**
     * @desc Function to check device name exist for removed device
     * @param $childId,$deviceName
     * @author suman khatri on 10th February 2014
     * @return ArrayObject
     */
    public function checkRemoveDeviceNameExist($childId, $deviceName, $childMobileNo) {
        $where = "child_id = $childId and device_name = '$deviceName'";
        if (!empty($childMobileNo) && $childMobileNo != null) {
            $where .= "and phone_number = '$childMobileNo'";
        }
        $deviceInfo = $this->_tblDeviceInfo->fetchRow($where);
        //echo "<pre>";print_r($deviceInfo);die;
        return $deviceInfo; //returns array
    }

    /**
     * @desc Function to update device detail with new info
     * @param $deviceId,$childId,$unqueCodeGenerate,$date,$childMobileNo,$deviceName,$emailId
     * @author suman khatri on 10th February 2014
     * @return $result
     */
    public function updateDeviceDataWithNewInfo($deviceId, $childId, $unqueCodeGenerate, $date, $childMobileNo, $deviceName, $emailId, $deviceType) {
        $updatePhoneData = array(
            'device_name' => $deviceName,
            'email_id' => $emailId,
            'longitude' => NULL,
            'latitude' => NULL,
            'device_lock_status' => 'UNLOCK',
            'phone_number' => $childMobileNo,
            'unique_key' => $unqueCodeGenerate,
            'device_key' => NULL,
            'device_configured' => 'N',
            'modified_date' => todayZendDate(),
            'device_monitored' => 'Y',
            'device_removed' => 'N',
            'registered_id' => NULL,
            'device_choose' => $deviceType
        );
        //echo $deviceId;
        //echo "<pre>";print_r($updatePhoneData);die;
        //updates data into DB
        $result = $this->_tblDeviceInfo->UpdateDevicePhoneData($updatePhoneData, $deviceId);
        return $result; //returns result
    }

    /**
     * @desc Function to get all devices of child whose log is generated for child
     * @param $childId
     * @author suman khatri on 10th February 2014
     * @return ArrayIterator
     */
    public function getChildAllUsedDeviceForChild($childId) {
        //getting all devices
        $allDevices = $this->_tblDeviceInfo->getChildAllAddedDeviceForChild($childId);
        return $allDevices; //returns array
    }

    /**
     * @desc Function to update device detail with new info
     * @param $deviceId,$childId,$unqueCodeGenerate,$date,$childMobileNo,$deviceName,$emailId
     * @author suman khatri on 18th February 2014
     * @return $result
     */
    public function updateDeviceVersionInfo($deviceId, $version) {
        $updatePhoneData = array(
            'version' => $version
        );
        //updates data into DB
        $result = $this->_tblDeviceInfo->UpdateDevicePhoneData($updatePhoneData, $deviceId);
        return $result; //returns result
    }

    /**
     * @desc Function to check device is exist using unique kay 
     * @param $uniqueKey, $device_key
     * @author suman khatri on 4th April 2014
     * @return ArrayObject
     */
    public function checkDeviceExistUsingUniqueOrDeviceKey($uniqueKey, $device_key) {
        $where ='';
        // check device is exist or not 
        if (!empty($uniqueKey) && $uniqueKey != null) {
            $where = "unique_key  = '" . $uniqueKey . "'";
        }
        
        if (!empty($device_key) && $device_key != null) {
            if (!empty($where) && $where != null) {
                $where .= "and device_key = '" . $device_key . "'";
            } else {
                $where = "device_key = '" . $device_key . "'";
            }
        }
        $deviceExist = $this->_tblDeviceInfo->CheckExistance($where);
        return $deviceExist; //returns result
    }

    /**
     * @desc Function to check device is removed or not 
     * @param  $device_key
     * @author suman khatri on 4th April 2014
     * @return ArrayObject
     */
    public function checkDeviceRemoved($device_key) {
        // check device is exist or not 
        $where = "device_key  = '" . $device_key . "' and device_removed != 'Y'";
        $deviceRemoved = $this->_tblDeviceInfo->CheckExistance($where);
        return $deviceRemoved; //returns result
    }

    /**
     * @desc Function to add device
     * @param  $device_key
     * @author suman khatri on 4th April 2014
     * @return last inserted id
     */
    public function AddDeviceForLiteMode($data) {
        // add device 
        $deviceId = $this->_tblDeviceInfo->addDeviceData($data);
        return $deviceId; //returns result
    }
    
    /**
     * @desc Function to get all child with configured device
     * @param NILL
     * @author suman khatri on 28th April 2014
     * @return count
     */
    public function getAllChildWithConfiguredDevice() {
        $deviceData = $this->_tblDeviceInfo->getAllChildWithConfiguredDevice(); //fetch device number and data
        return count($deviceData); //returns array
    }

}
