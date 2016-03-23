<?php
/*
 * This is a model class for Cms Content
 * Created By Sunil Khanchandani
 * August 13 2013 
 */
class Application_Model_DbTable_LockDeviceFor extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_lock_device_for';
	
	/**
	 * function to insert data 
	 * @param data
	 * @return last inserted id
	 * created by suman on 24 March 2014
	 */ 
	public function insertData($insertData)
	{
		return $this->insert($data);
	}
	
	/**
	 * function to update data
	 * @param data , id
	 * @return no of affected rows
	 * created by suman on 24 March 2014
	 */
	public function updateData($id,$updateData)
	{
	
		$where = $this->_db->quoteInto("id = ?",$id);
	
		return $this->update($updateData,$where);
	}
	
	/**
	 * function to get all data 
	 * @param nill
	 * @return ArrayIterator
	 * created by suman on 24 March 2014
	 */ 
	public function getAllLockDeviceTime()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bldf' => 'bal_lock_device_for'),
				array('bldf.lock_device_for'));
		$allData = $db->fetchAll($select);
		return $allData;
	}
	
	/**
	 * function to delete data
	 * @param id
	 * @return no of affected rows
	 * created by suman on 24 March 2014
	 */
	public function deleteData($id)
	{
		$where = $this->_db->quoteInto("id = ?",$id);
		return $this->delete($where);
	}
}