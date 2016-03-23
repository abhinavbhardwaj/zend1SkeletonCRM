<?php
/*
 * This is a model class for Child Apps Log Information
 * Created By Sunil Khanhchandani Khatri
 * 9 August 2013 
 */
class Application_Model_DbTable_ChildAppsLog extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_child_device_app_log';
	
	
	
	
	
	/*
	 * This is a function to get DeviceKey on basis of unique key
	 */
	
	public function getDeviceKey($uniqueKey)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where= $db->quoteInto("unique_key=?", $uniqueKey);
		$select = $db->select()
					 ->from($this->_name,'device_key')
					 ->where($where);
		$getDeviceKey = $db->fetchRow($select);
		return $getDeviceKey;
	}
	
	
	/*
	 * This is a function to check mobile number already exist or not
	 * @param child mobile number
	 */
	
	public function CheckMobileAlreadyRegister($childPhoneNumber)
	{
		$where = $this->_db->quoteInto("phone_number=?",$childPhoneNumber);
		$checkPhoneNumberExist = $this->fetchRow($where);
		if($checkPhoneNumberExist){
			return true;
		}else{
			return false;
		}
	}
	
	public function CheckMobileAlreadyRegisterUpdate($childPhoneNumber,$chilId)
	{
		$where = $this->_db->quoteInto("phone_number=?",$childPhoneNumber);
		$where .= $this->_db->quoteInto(" AND child_id!=?",$chilId); 
		$checkPhoneNumberExist = $this->fetchRow($where);
		if($checkPhoneNumberExist){
			return true;
		}else{
			return false;
		}
	}
	
public function CheckMobileAlreadyRegisterOrNot($childPhoneNumber)
	{
		$where = $this->_db->quoteInto("phone_number=?",$childPhoneNumber);
		
		$checkPhoneNumberExist = $this->fetchRow($where);
		if($checkPhoneNumberExist){
			return true;
		}else{
			return false;
		}
	}
	

	//function is used to get child device info
	public function getChildDeviceId($childId)
	{
		$where = "child_id = $childId";
		$where .= " AND device_removed = 'N'";
		return $this->fetchAll($where)->toArray();
	}
	
//function is used to get child device info
	public function getChildAllDevice($childId)
	{
		$where = $this->_db->quoteInto("child_id =?",$childId);
		$where .= " AND device_removed = 'N'";
		return $this->fetchAll($where)->toArray();
	}
	
	
//function is used to get child device info
	public function getChildDeviceData($deviceId)
	{
		$where = $this->_db->quoteInto("child_device_id =?",$deviceId);
		$where .= " AND device_removed = 'N'";
		return $this->fetchRow($where);
	}
	
	public function UpdateDevicePhoneData($data,$deviceId)
	{
		$where = $this->_db->quoteInto("child_device_id =?", $deviceId);
		return $this->update($data , $where);
	}
}	