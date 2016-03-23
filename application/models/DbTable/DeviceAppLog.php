<?php
/*
 * This is a model class for Device App log Information
 * Created By Suman Khatri
 * Thursday, August 08 2013 
 */
class Application_Model_DbTable_DeviceAppLog extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_child_device_app_log';
	
	/*
	 * this function is used to add device app log
	 * @param data and device key and device log
	 * created by suman
	 * on August 07 2013
	 */
	public function AddDeviceApplog($data)
	{
		return $this->insert($data);
	}
	
	/**
     * @desc Function to get device apps log for date range
     * @param $childDeviceId, $toDate, $fromDate
	 * @author suman khatri on 13th November 2013
	 * @return ArrayIterator
     */
	public function getAppsLog($childDeviceId, $toDate, $fromDate,$sOrder=null,$sortOr=null,$sWhere=null,$childId)
	{		
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $db->quoteInto("bl.child_device_id = ?",$childDeviceId);
                $where .= $db->quoteInto(" and bl.child_id = ?",$childId);
		$where .= " AND (bl.date >= '$fromDate' and bl.date <= '$toDate' and bli.duration != 0)";
		if(!empty($sOrder) && $sOrder != null){
			$order = $sOrder." ".$sortOr;
		}else{
			$order = "duration desc";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		$select = $db->select()
					 ->from(array('bl' =>'bal_child_device_app_log'))
					 ->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
    	 				'bl.log_id = bli.log_id',
    	 				array('app_name','package_name','app_image','app_id',
    	 				"SUM(if(bli.duration != 0,bli.duration,0)) as duration"))
    	 				/*->joinLeft(array('blda' => 'bal_child_device_apps'),
    	 						'blda.app_id = bli.app_id',
    	 						array('app_id'))*/
    	 				->where($where)->group("bli.package_name")->order($order);
		$fetchAppsLogDetails = $db->fetchAll($select);
		return $fetchAppsLogDetails;
		
	}
	
	/**
     * @desc Function to get device apps log for today
     * @param $childDeviceId, $toDate, $fromDate
	 * @author suman khatri on 13th November 2013
	 * @return ArrayIterator
     */
	public function getAppLogforToday($childDeviceId,$app_id,$childId)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $db->quoteInto("bl.child_device_id = ?",$childDeviceId);
                $where .= $db->quoteInto(" and bl.child_id = ?",$childId);
		$date = date('Y-m-d');
		$where .= " AND bl.date = '$date'";
		$where .= " AND bli.app_id = $app_id";
		$select = $db->select()
					 ->from(array('bl' =>'bal_child_device_app_log'))
					 ->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
    	 				'bl.log_id = bli.log_id',
    	 				array('app_name','package_name','duration','app_id'))
    	 				->where($where);
					//echo $select; die;
		$fetchAppsLogDetails = $db->fetchAll($select); 		 
		return $fetchAppsLogDetails;
		
	}
	
    /**
    * @desc Function to get log for app for date range
    * @param $childDeviceId, $date
    * @author suman khatri on  9th jan 2014
    * @return ArrayIterator
    */
	public function getLogForApp ($appId, $date,$deviceId,$packegaName, $childId = NULL)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $db->quoteInto("bli.package_name = ?",$packegaName);
		$where .= " AND (bl.date = '$date')";
		$where .= " AND (bl.child_device_id = '$deviceId')";
		$where .= " AND (bl.child_id = '$childId')";
		$select = $db->select()
					->from(array('bl' =>'bal_child_device_app_log'))
					->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
    	 				'bl.log_id = bli.log_id',
    	 				array('app_name','package_name','app_image','app_id',"SUM(duration) as duration"))
    	 			->where($where);
		$fetchAppsLogDetails = $db->fetchAll($select);
		$fetchAppsLogDetails = $fetchAppsLogDetails[0];
		//echo "<pre>";print_r($fetchAppsLogDetails);die;
		return $fetchAppsLogDetails;
		/*$where = $db->quoteInto("bli.app_id = ?",$appId);
		$where .= " AND (bl.date = '$date')";
		$select = $db->select()
					->from(array('bl' =>'bal_child_device_app_log'))
					->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
    	 				'bl.log_id = bli.log_id',
    	 				array('app_name','package_name','app_image','app_id','duration'))
    	 			->where($where);
		$fetchAppsLogDetails = $db->fetchRow($select);
		//echo "<pre>";print_r($fetchAppsLogDetails);die;
		return $fetchAppsLogDetails;*/
		
	}
	
    /**
    * @desc Function to app name log for app for date range
    * @param $appId
    * @author suman khatri on  9th jan 2014
    * @return ArrayIterator
    */
    public function getAppName ($appId)
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $db->quoteInto("bli.app_id = ?",$appId);
        $select = $db->select()
                                ->from(array('bl' =>'bal_child_device_app_log'))
                                ->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
                                'bl.log_id = bli.log_id',
                                array('app_name','package_name','app_image','app_id','duration'))
                        ->where($where)->order("bl.date desc");
        $fetchAppsDetails = $db->fetchRow($select);
        return $fetchAppsDetails;
		
    }
	/***************
	 * function for delete all Apps loga tables
	* @param deviceId Int
	* return int
	*
	* *************************/
	public function deleteData($childDeviceId)
	{
		$where = $this->_db->quoteInto("child_device_id = ?",$childDeviceId);
		return $this->delete($where);
	} 
        
        /**
        * @desc Function to get log for application used by chilren
        * @param $childDeviceId, $date
        * @author suman khatri on 15th April 2014
        * @return ArrayIterator
        */
	public function getLogForAllAppsUsedByChild ()
	{
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$order = "duration desc";
		$select = $db->select()
					 ->from(array('bl' =>'bal_child_device_app_log'),array(''))
					 ->joinLeft(array('bli' => 'bal_child_device_app_log_details'),
    	 				'bl.log_id = bli.log_id',
    	 				array('app_name'))
                        ->joinLeft(array('bcdr' => 'bal_child_device_relation'),
    	 				'bcdr.device_id = bl.child_device_id and bcdr.child_id = bl.child_id',
    	 				array("bcdr.child_id"))
                                        ->where('bli.duration != 0')
    	 				->group(array("bcdr.child_id","bli.package_name"))
                                        //->order($order)
                                        ->limit(10);
            $fetchAppsLogDetails = $db->fetchAll($select);
            return $fetchAppsLogDetails;
	}
}	