<?php
/*
 * This is a model class for usersubscription email
 * Created By Suman Khatri
 * September 26 2014
 */
class Application_Model_DbTable_UserSubscribe extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_subscribers';
	
        
        
        /*
	 * this function is used to isert email data 
	 * @param page title
	 * @param update data
	 * 
	 */
	public function addData($insertData)
	{		
            return $this->insert($insertData);
	}   
        
        
}	