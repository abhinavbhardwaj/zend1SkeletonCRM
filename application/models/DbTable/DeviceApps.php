<?php
/*
 * This is a model class for Device App Information
 * Created By Suman Khatri
 * Thursday, August 07 2013 
 */
class Application_Model_DbTable_DeviceApps extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_child_device_apps';
	
	/*
	 * this function is used to add device app
	 * @param data and device key
	 * created by suman
	 * on August 07 2013
	 */
	public function AddDeviceApps($data)
	{
		return $this->insert($data);
	}
	
	
//function is used to get child device info
	public function getAllChildDeviceApps($childDeviceId, $childId)
	{
		$where = $this->_db->quoteInto("child_device_id = ?",$childDeviceId); 
                $where .= $this->_db->quoteInto(" and child_id = ?",$childId); 
		$where  .= " and unproductive = 'N'";
		$result = $this->fetchAll($where,"app_name ASC")->toArray();
		return $result;
	}
	
//function is used to get child device info
	public function getAllChildUnproductiveDeviceApps($childDeviceId, $childId)
	{
		$where = $this->_db->quoteInto("child_device_id = ?",$childDeviceId); 
                $where .= $this->_db->quoteInto(" and child_id = ?",$childId); 
		$where .= " AND unproductive = 'Y'";
		return $this->fetchAll($where,"app_name ASC")->toArray();
	}
        
        
        public function getAllAppsInDevice($childDeviceId, $childId, $excludeFinny = TRUE) 
        {
            $where = array(
                "child_device_id = ?" => $childDeviceId,
                "child_id = ?" => $childId
            );
            
            if($excludeFinny) {
                $where["package_name <> ?"] = FINNY_PACKAGE_NAME;
            }
            
            $result = $this->fetchAll($where,"app_name ASC");
            return $result->toArray();
        }
        
	public function UpdateAppsData($data,$appsId)
	{
		$where = $this->_db->quoteInto("app_id =?", $appsId);
		$result = $this->update($data , $where);
		return $result;
	}
        
        public function UpdateAppsProdUnprod($appData, $appId)
	{
		$where = $this->_db->quoteInto("app_id =?", $appId);
		$result = $this->update($appData , $where);
		return $result;
	}
	
	/*
	 * this function is used to check device app existance
	 * @param where condition
	 * created by suman
	 * on August 08 2013
	 */
	public function CheckAppExistance($where)
	{
		$data	= $this->fetchRow($where);
		if($data)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/*
	 * this function is used to get device apps
	 * @param $childId, $deviceId
	 * created by suman
	 * on August 08 2013
	 */
	public function GetAllApps($childId, $deviceId)
	{
            $where = $this->_db->quoteInto("child_id =?", $childId);
            $where .= $this->_db->quoteInto(" and child_device_id = ?", $deviceId); 
            return $this->fetchAll($where,"app_name ASC");
	}
	
	
	/*
	 * this function is used to remove device app
	 * @param data and device key
	 * created by suman
	 * on August 09 2013
	 */
	public function removeDeviceApps($where)
	{
		return $this->delete($where);
	}
	/**************function for return device id from app id********************/
	public function getDiviceId($appId){
		return $this->fetchRow("app_id ='$appId'");
	}
        
        
    /**
    * @desc Function to get over all App found in system
    * @param NILL
    * @author suman khatri on 15th april 2014
    * @return result
    */
    public function getOverAllApps ()
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()
                                ->from(array('bla' =>'bal_child_device_apps'))
                                       // array("SUM(if(bla.package_name != '',1,0)) as TotalCount"))
                                ->group('bla.package_name');
        $allApps = $db->fetchAll($select);
        return count($allApps);
    }
    
    /**
     * @desc Function to delete apps for child and device
     * @param $childDeviceId
     * @author suman khatri on 9th October 2014
     * @return result
     */
    public function removeDeviceAppsForChildAndDevice($childDeviceId)
    {
        $result = $this->delete(array("child_device_id = ?" => $childDeviceId)); //deletes record according to condition 
        return $result; //returns result
    }

}	