<?php
/*
 * This is a model class for Child Subjects Information
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */
class Application_Model_DbTable_QuestionOptions extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_question_options';
	
	public function getAnswer($optionId)
	{
		$where 			= "question_option_id = $optionId";
		$answer 		= $this->fetchRow($where);
		return $answer;
	}
	
	//function to get option for a particular question
	public function getOptionforQuestion($qId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
    	 $select = $db->select()
    	 ->from(array('bqo' => 'bal_question_options'),
    	 		array('bqo.*'))
    	 		->where("bqo.question_id = $qId")
    	 		->order('bqo.option');
    	$childInfo =  $db->fetchAll($select);
		return $childInfo;
		//$order			= "order by option";
		/*$where 			= "question_id = $qId order by rand()";
		$options 		= $this->fetchAll($where);
		return $options;*/
	}
	
	//function to add option for a particular question
	public function addOptionforQuestion($data)
	{
		return $this->insert($data);
	}
	
	//function to update options 
	public function updateOptionforQuestion($dataOrupdate,$whererighto)
	{
		return $this->update($dataOrupdate, $whererighto);
	}
	
	public function deleteOptions($where)
	{
		return $this->delete($where);
	}
}