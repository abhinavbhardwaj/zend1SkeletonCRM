<?php
/*
 * This is a model class for Device App log detailInformation
 * Created By Suman Khatri
 * Thursday, August 08 2013 
 */
class Application_Model_DbTable_DeviceAppLogDetail extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_child_device_app_log_details';
	
	/*
	 * this function is used to add device app log detail
	 * @param data and device key and log detail
	 * created by suman
	 * on August 07 2013
	 */
	public function AddDeviceApplogDetail($data)
	{
		return $this->insert($data);
	}
	
	/*
	 * this function is used to update device app log detail
	 * @param data and device key and log detail
	 * created by suman
	 * on August 07 2013
	 */
	public function updateDeviceApplogDetail($data,$where)
	{
		return $this->update($data, $where);
	}
	/***************
	 * function for delete all apps data from this table
	 * @param appId int
	 * return int
	 * 
	 * *************************/
	public function deleteData($appId)
	{
		$where = $this->_db->quoteInto("app_id = ?",$appId);
		return $this->delete($where);
	}

        
        public function getTopFiveAppUsedByChild($weeklyGoalDateRange,$childId) {
            if(!empty($weeklyGoalDateRange)){
		$startDate = date("Y-m-d",  strtotime($weeklyGoalDateRange[0]['start_date']));
		$endDate = date("Y-m-d",  strtotime($weeklyGoalDateRange[0]['end_date']));
                $where = $this->_db->quoteInto("al.date >= ?",$startDate);
                $where .= $this->_db->quoteInto("and al.date <= ?",$endDate);
            } else {
                $where = 1;
            }
            $whereChild = $this->_db->quoteInto("al.child_id = ?",$childId);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('ald' => 'bal_child_device_app_log_details'),
                    array('ald.app_name', "SUM(ald.duration) AS duration", 'ald.package_name','ald.app_image'))
                ->joinLeft(array('al' => 'bal_child_device_app_log'),
                                'al.log_id = ald.log_id',
                    array(''))
                ->where($whereChild)
                ->where($where)
                ->where("ald.duration > 0")
                ->group("ald.package_name")
                ->order("duration DESC")
                ->limit(5);
            $logData = $db->fetchAll($select);
            return $logData;
        }
        
        
        public function getAppsLogToday($childId,$deviceId) {
            $toDay = date('Y-m-d' , strtotime(todayZendDate()));
            $where = $this->_db->quoteInto("al.date = ?",$toDay);
            $whereChild = $this->_db->quoteInto("al.child_id = ?",$childId);
            $whereChild .= $this->_db->quoteInto(" and al.child_device_id = ?",$deviceId);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('ald' => 'bal_child_device_app_log_details'),
                    array('ald.app_name', "SUM(ald.duration) AS duration", 'ald.package_name','ald.app_image'))
                ->joinLeft(array('al' => 'bal_child_device_app_log'),
                                'al.log_id = ald.log_id',
                    array(''))
                ->where($whereChild)
                ->where($where)
                ->where("ald.duration > 0")
                ->group("ald.package_name")
                ->order("duration DESC");
            $logData = $db->fetchAll($select);
            return $logData;
        }
        
        public function getAppsLogThisWeek($childId,$deviceId) {
            //getting spent time for week
            if (date('w') == 0) {
                $firstDayOfWeek = date('Y-m-d', strtotime(todayZendDate()));
            } else {
                $firstDayOfWeek = date('Y-m-d', strtotime('Last Sunday', time()));
            }
            $lastDayOfWeek = date('Y-m-d', strtotime('+6 days', strtotime($firstDayOfWeek)));
            $where = $this->_db->quoteInto("al.date >= ?",$firstDayOfWeek);
            $where .= $this->_db->quoteInto(" and al.date <= ?",$lastDayOfWeek);
            $whereChild = $this->_db->quoteInto("al.child_id = ?",$childId);
            $whereChild .= $this->_db->quoteInto(" and al.child_device_id = ?",$deviceId);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('ald' => 'bal_child_device_app_log_details'),
                    array('ald.app_name', "SUM(ald.duration) AS duration", 'ald.package_name','ald.app_image'))
                ->joinLeft(array('al' => 'bal_child_device_app_log'),
                                'al.log_id = ald.log_id',
                    array(''))
                ->where($whereChild)
                ->where($where)
                ->where("ald.duration > 0")
                ->group("ald.package_name")
                ->order("duration DESC");
            $logData = $db->fetchAll($select);
            return $logData;
        }
        
        public function getAppsLogThisMonth($childId,$deviceId) {
            $startDateOfMonth = date('Y-m-1' , strtotime(todayZendDate()));
            $endDateOfMonth = date('Y-m-d' , strtotime(todayZendDate()));
            $where = $this->_db->quoteInto("al.date >= ?",$startDateOfMonth);
            $where .= $this->_db->quoteInto(" and al.date <= ?",$endDateOfMonth);
            $whereChild = $this->_db->quoteInto("al.child_id = ?",$childId);
            $whereChild .= $this->_db->quoteInto(" and al.child_device_id = ?",$deviceId);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('ald' => 'bal_child_device_app_log_details'),
                    array('ald.app_name', "SUM(ald.duration) AS duration", 'ald.package_name','ald.app_image'))
                ->joinLeft(array('al' => 'bal_child_device_app_log'),
                                'al.log_id = ald.log_id',
                    array(''))
                ->where($whereChild)
                ->where($where)
                ->group("ald.package_name")
                ->where("ald.duration > 0")
                ->order("duration DESC");
            $logData = $db->fetchAll($select);
            return $logData;
        }
        

}	