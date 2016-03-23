<?php
/*
 * This is a model class for notifications to parent regarding App log Information
 * Created By Suman Khatri
 * Thursday, September 20 2013 
 */
class Application_Model_DbTable_ParentNotificationStatus extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_parent_notification_status';
	
	/*
	 * this function is used to get notification record in the table
	 * @param data notification id
	 * created by suman
	 * on September 20 2013
	 */
	public function getNotificationStatus($nId)
	{
		$where = "notification_id = $nId and status = 'Y'";
		$nStatus =  $this->fetchRow($where);
		if(!empty($nStatus)){
			return true;
		}else 
		{
			return false;
		}
	}
	
	/*
	 * @desc this function is used to add notification record in the table
	 * @param aaray data
	 * @author suman on September 20 2013
	 * @return last insert id
	 */
	public function addNotificationStatus($data)
	{
		return $this->insert($data);
	}
	
	/*
	 * @descthis function is used to update notification record in the table
	 * @param int id , array data to be updated
	 * @return no of affected rows
	 * @author Suman Khatri on 09-10-2013 
	 */
	public function updateNotificationStatus($data,$id)
	{
		$where = "id = $id";
		return $this->update($data, $where);
	}
}	