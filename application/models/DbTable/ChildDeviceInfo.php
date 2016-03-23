<?php

/*
 * This is a model class for Child Device Information
 * Created By Suman Khatri
 * Thursday, July 16 2013 
 */

class Application_Model_DbTable_ChildDeviceInfo extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_child_devices';

    /*
     * this function is used to add device 
     * @param data and device key
     * created by suman
     * on 16th july 2013 
     */

    public function UpdateDeviceKey($data, $where) {
        return $this->update($data, $where);
    }

    public function CheckExistance($where) {
        $deviceExist = $this->fetchRow($where);
        if ($deviceExist) {
            return $deviceExist;
        } else {
            return false;
        }
    }

    public function addDeviceData($data) {
        return $this->insert($data);
    }

    /*
     * This is a function to get DeviceKey on basis of unique key
     */

    public function getDeviceKey($uniqueKey) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $db->quoteInto("unique_key=?", $uniqueKey);
        $select = $db->select()
                ->from($this->_name)
                ->where($where);
        $getDeviceKey = $db->fetchRow($select);
        return $getDeviceKey;
    }

    /*
     * This is a function to check mobile number already exist or not
     * @param child mobile number
     */

    public function CheckMobileAlreadyRegister($childPhoneNumber, $childDeviceId) {

        $where = $this->_db->quoteInto("phone_number=?", $childPhoneNumber);
        $where .= " AND device_removed = 'N'";
        if (!empty($childDeviceId)) {
            $where .= " AND child_device_id <> '$childDeviceId'";
        }
        $result = $this->fetchRow($where);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This is a function to check device name already exist or not
     * @param child mobile number
     */

    public function CheckDeviceAlreadyRegister($deviceName, $childDeviceId, $childId) {

        $where = $this->_db->quoteInto("device_name=?", $deviceName);
        $where .= " AND device_removed = 'N'";
        if (!empty($childDeviceId)) {
            $where .= " AND child_device_id <> '$childDeviceId'";
        }
        if (!empty($childId)) {
            $where .= " AND child_id  = '$childId'";
        }
        $result = $this->fetchRow($where);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckMobileAlreadyRegisterUpdate($childPhoneNumber, $chilId) {
        $where = $this->_db->quoteInto("phone_number=?", $childPhoneNumber);
        if (!empty($chilId)) {
            $where .= $this->_db->quoteInto(" AND child_id!=?", $chilId);
        }
        $checkPhoneNumberExist = $this->fetchRow($where);
        if ($checkPhoneNumberExist) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckMobileAlreadyRegisterOrNot($deviceName, $childId, $action, $deviceId) {
        $where = $this->_db->quoteInto("child_id =?", $childId);
        $where .= $this->_db->quoteInto(" and device_name =?", $deviceName);
        if ($action == 'update') {
            $where .= $this->_db->quoteInto(" and child_device_id !=?", $deviceId);
        }
        $where .= " AND device_removed = 'N'";
        $checkPhoneNumberExist = $this->fetchRow($where);
        if ($checkPhoneNumberExist) {
            return true;
        } else {
            return false;
        }
    }

    //function is used to get child device info
    public function getChildDeviceId($childId) {
        $where = "child_id = $childId";
        $where .= " AND device_removed = 'N'";
        return $this->fetchAll($where)->toArray();
    }

//function is used to get child device info
    public function getChildAllDevice($childId) {

        $where = $this->_db->quoteInto("child_id =?", $childId);
        $where .= " AND device_removed = 'N'";
        $order = "child_device_id DESC";
        //$order = "created_date DESC";
        return $this->fetchAll($where, $order)->toArray();
    }

    //function is used to get child device info
    public function getChildWithAllDevice($childId) {

        $where = $this->_db->quoteInto("child_id =?", $childId);
        //$where .= " AND child_device_id > '0'";
        $order = "child_device_id DESC";
        //$order = "created_date DESC";
        return $this->fetchAll($where, $order)->toArray();
    }

//function is used to get child device info
    public function getChildDeviceData($deviceId) {
        $where = $this->_db->quoteInto("child_device_id =?", $deviceId);
        $where .= " AND device_removed = 'N'";
        return $this->fetchRow($where);
    }

    public function UpdateDevicePhoneData($data, $deviceId) {
        $where = $this->_db->quoteInto("child_device_id =?", $deviceId);
        $res = $this->update($data, $where);
        return $res;
    }

    /*     * ***************function for get device id for send challenges 
     * @param int childId
     * @return array
     * ************************** */

    public function getDeviceId($childId) {
        $where = "child_id = '$childId' AND registered_id <> ''";
        return $this->fetchAll($where);
    }

    /*     * ***************function for get device id for Apps list by jquery
     * @param int device_key
     * @return array
     * @Dharmendra Mishra
     * ************************** */

    public function getDeviceIdDeviceKey($deviceKey, $uniqueId) {
        if (isset($deviceKey, $uniqueId) && !empty($deviceKey) && !empty($uniqueId)) { //if to check  $deviceKey and $uniqueId is not null
            $where = "device_key = '$deviceKey'"; //create whare variables
            $where .= " AND  unique_key = '$uniqueId' AND  registered_id <> ''";
        }
        return $this->fetchRow($where);
    }

    /*     * ************function for geting child device list
     * @param $child Id
     * @return Array
     * @Dharmendra Mishra
     */

    public function getChildAllDeviceForChild($childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bch' => 'bal_child_devices'), array("bch.*"))
                ->where("bch.device_monitored = 'Y'")
                ->where("bch.device_configured = 'Y'")
                ->where("bch.device_monitored = 'Y'")
                ->where("bch.device_removed = 'N'")
                ->where("bch.child_id = $childId")
                ->order(array('bch.child_device_id desc'));
        $deviceData = $db->fetchAll($select);
        return $deviceData;
    }

    public function updateDeviceInfo($unqueCodeGenerate, $date, $childMobileNo, $deviceName, $emailId, $where) {
        if (!empty($unqueCodeGenerate)) {
            $Data[unique_key] = $this->_db->quoteInto($unqueCodeGenerate);
        }
        if (!empty($date)) {
            $Data[created_date] = $this->_db->quoteInto($date);
        }
        if (!empty($childMobileNo)) {
            $Data[phone_number] = $this->_db->quoteInto($childMobileNo);
        }
        if (!empty($deviceName)) {
            $Data[device_name] = $this->_db->quoteInto($deviceName);
        }
        if (!empty($emailId)) {
            $Data[email_id] = $this->_db->quoteInto($emailId);
        }
        return $this->update($Data, $where);
    }

    public function insertDeviceInfo($childId, $unqueCodeGenerate, $date, $childMobileNo, $deviceName, $emailId, $type) {
        if (!empty($childId)) {
            $Data['child_id'] = $this->_db->quoteInto($childId);
        }
        if (!empty($unqueCodeGenerate)) {
            $Data['unique_key'] = $this->_db->quoteInto($unqueCodeGenerate);
        }
        if (!empty($date)) {
            $Data['created_date'] = $this->_db->quoteInto($date);
        }
        if (!empty($childMobileNo)) {
            $Data['phone_number'] = $this->_db->quoteInto($childMobileNo);
        }
        if (!empty($deviceName)) {
            $Data['device_name'] = $this->_db->quoteInto($deviceName);
        }
        if (!empty($emailId)) {
            $Data['email_id'] = $this->_db->quoteInto($emailId);
        }
        if (!empty($type)) {
            $Data['device_choose'] = $this->_db->quoteInto($type);
        }
        return $this->insert($Data);
    }

    public function getChildInfoUsingDeviceId($deviceId) {
        $where = "child_device_id = '$deviceId'";
        return $this->fetchRow($where);
    }

    public function CheckExpiredDevice($childId, $deviceName, $uniqueCodeExp) {
        //$wherePhoneCheck = "child_id = '$childId' AND device_name ='$deviceName' AND unique_key <> '$uniqueCodeExp'";
        $where = $this->_db->quoteInto("child_id =?", $childId);
        $where .= $this->_db->quoteInto(" and device_name =?", $deviceName);
        $where .= $this->_db->quoteInto(" and unique_key <>?", $uniqueCodeExp);
        return $this->fetchRow($where);
    }

    /**
     * @desc Function to get all added devices of child
     * @param $childId
     * @author suman khatri on 10th February 2014
     * @return ArrayIterator
     */
    public function getChildAllAddedDeviceForChild($childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('cdr' => 'bal_child_device_relation'), array('*', 'Max(cdr.is_associated) as is_associated_max'))
                ->join(array('di' => 'bal_device_info'), 'di.device_id = cdr.device_id', array('di.*', 'di.device_id as child_device_id'))
                ->where("cdr.child_id = ?", $childId)
            //    ->order(array('cdr.date_association desc'))
                ->order('is_associated_max DESC')
                ->group('di.device_id');
        $deviceData = $db->fetchAll($select);
        return $deviceData;
    }

    /**
     * @desc Function to get all added devices of child whose log is generated
     * @param $childId
     * @author suman khatri on 21th February 2014
     * @return ArrayIterator
     */
    public function getChildAllUsedDeviceForChild($childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bch' => 'bal_child_devices'), array("bch.*"))
                ->joinLeft(array('blap' => 'bal_child_device_app_log'), 'blap.child_device_id = bch.child_device_id', array('log_id'))
                ->joinLeft(array('blapd' => 'bal_child_device_app_log_details'), 'blapd.log_id = blap.log_id', array('duration'))
                ->where("bch.device_configured = 'Y'")
                ->where("bch.child_id = $childId")
                ->where("blapd.duration != 0 and blapd.duration is not null")
                ->order(array('bch.created_date desc'))
                ->group('bch.device_name');
        $deviceData = $db->fetchAll($select);
        return $deviceData;
    }

    /*     * *************
     * function for delete device info
     * @param childId Int
     * return int
     *
     * ************************ */

    public function deleteData($childId) {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->delete($where);
    }

    /**
     * @desc Function to get all child with configured device
     * @param NILL
     * @author suman khatri on 28th April 2014
     * @return ArrayIterator
     */
    public function getAllChildWithConfiguredDevice() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('this' => $this->_name), array("this.child_device_id"))
                ->joinLeft(array('blc' => 'bal_children'), 'blc.child_id = this.child_id and blc.parent_id != 0', array('blc.child_id'))
                ->where("this.device_configured = 'Y' AND this.device_key != '' AND this.device_removed = 'N' and blc.child_id is not null")
                ->group("this.child_id");
        $result = $db->fetchAll($select);
        return $result;
    }

}
