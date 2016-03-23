<?php
/*
 * This is a model class for Device Information
 * Created By Suman Khatri
 * October 07 2014
 */
class Application_Model_DbTable_DeviceInfo extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_device_info';	

    /**
     * @desc Function to add device info into DB
     * @param array $insertData
     * @author suman khatri on 8th October 2014
     * @return Last insert Id
     */
    public function addDeviceInfo($insertData)
    {
        return $this->insert($insertData); //inserts device data and return last insert Id
    }
    
    /**
     * @desc Function to update device info
     * @param array $updateData,$deviceId
     * @author suman khatri on October 08 2014
     * @return $result
     */
    public function updateDeviceInfo($updateData,$deviceId) {
        $where = $this->_db->quoteInto("device_id = ?",$deviceId); 
        $result = $this->update($updateData,$where); //update device data 
        return $result; //returns $result
    }
    
    /**
     * @desc Function to check device is already exist or not in bal_device_info
     * @param $deviceKey
     * @author suman khatri on October 08 2014
     * @return result
     */
    public function checkDeviceExistOrNotInDeviceInfo($deviceKey) {
        $where = $this->_db->quoteInto("device_key = ?",$deviceKey); 
        $result = $this->fetchRow($where); //update device data 
        return $result; //returns $result
    }
    
}	