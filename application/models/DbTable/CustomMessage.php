<?php
/*
 * This is a model class for custom messages
 */
class Application_Model_DbTable_CustomMessage extends Zend_Db_Table_Abstract {
	// This is the name of Table
	protected $_name = 'bal_parent_custom_message';
	/*
	 * This is a function to get custom message to send perticuler child device when questions are tiger his/her device @param childId @return array
	 */
	public function getCustomMesage($childId) {
		$where = "child_id = $childId";
		$result = $this->fetchRow ( $where );
		return $result;
	}
	/**
	 * ***************function for updated custommessage
	 *
	 * @param
	 *        	array
	 * @param
	 *        	id
	 * @return int
	 */
	public function updateCustomMessage($data, $customId) {
		$where = "custom_message_id = $customId";
		return $this->update ( $data, $where );
	}
	/**
	 * ***************function for insert new custommessage
	 *
	 * @param
	 *        	array
	 * @param
	 *        	id
	 * @return int
	 */
	public function insertCustomMesage($data) {
		return $this->insert ( $data );
	}
	
	/******************function for get custome message*******************/
	public function getCustomMeassage($requestId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bpcm' => $this->_name),
				array('message'=>'bpcm.message'))
				->joinLeft(array('blqr' => 'bal_child_question_requests'),
						'bpcm.child_id = blqr.child_id',
						array())
						->where("blqr.request_id = $requestId");
				$custommesage =  $db->fetchRow($select);
				if(empty($custommesage)){
					$select = $db->select()
					->from(array('bcqr' => 'bal_child_question_requests'),
							array())
							->joinLeft(array('blc' => 'bal_children'),
								'bcqr.child_id = blc.child_id',
								array('name'=>'blc.name','firstname'=>'blc.firstname','lastname'=>'blc.lastname'))
								->joinLeft(array('blp' => 'bal_parents'),
										'blc.parent_id = blp.parent_id',
										array('parent_type'=>'blp.parent_type'))
							->where("bcqr.request_id = $requestId");
							$custommesage =  $db->fetchRow($select);
							
							if($custommesage['parent_type'] == 'D'){
								$type = 'Dad';
							}else{
								$type = 'Mom';
							}
							$name = ucwords($custommesage['firstname']);
					$custommesage['message'] = "Question from finny!";
					return $custommesage;
				}else {
					return $custommesage;
				}
				
	}
/**
	 * ***************function for remove custommessage
	 *
	 * @param
	 *        	array
	 * @param
	 *        	id
	 * @return int
	 */
	public function deleteCustomMessage($customId) {
		$where = "custom_message_id = $customId";
		return $this->delete($where);
	}
}	