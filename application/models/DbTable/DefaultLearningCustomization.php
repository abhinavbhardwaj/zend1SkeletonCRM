<?php
class Application_Model_DbTable_DefaultLearningCustomization extends Zend_Db_Table_Abstract
{ 
/*
	 * defined all object variables that are used in entire class
	 */
	protected $_name =  'bal_child_default_learning_customization';
	/*
	 * function for create all model table object used this object to call model table functions
	 */
	
	public function getLCData(){
		return $this->fetchRow();
	}
}