<?php
/*
 * This is a model class for Categories Standards
 * Created By Sunil Khanchandani
 * 
 */
class Application_Model_DbTable_CategoriesStandards extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_standards';
	
	
	//function to add standards
	public function fetchAllStandard($fieldArray)
	{
	
		$db =  Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = $db->select()
		->from($this->_name,$fieldArray); 
		$standardDetails = $db->fetchAll($select);
		return $standardDetails;

	
		
	}
	
}