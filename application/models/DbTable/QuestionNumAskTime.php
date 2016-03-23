<?php
/*
 * This is a model class for Cms Content
 * Created By Sunil Khanchandani
 * August 13 2013 
 */
class Application_Model_DbTable_QuestionNumAskTime extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_questionNum_askTime';
	
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
	 * function to get all question num data 
	 * @param nill
	 * @return ArrayIterator
	 * created by suman on 24 March 2014
	 */ 
	public function getQuestionNumData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bqna' => 'bal_questionNum_askTime'),
				array('bqna.questionNum'));
		$allData = $db->fetchAll($select);
		return $allData;
	}
	
	/**
	 * function to get all question num data
	 * @param nill
	 * @return ArrayIterator
	 * created by suman on 24 March 2014
	 */
	public function getAllAsktimeData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bqna' => 'bal_questionNum_askTime'),
				array('bqna.asktime'));
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
	
	/**
	 * function to get record using no. of question
	 * @param qNum
	 * @return ArrayObject
	 * created by suman on 25 March 2014
	 */
	public function getCorrespondentAsktime($qNum)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bqna' => 'bal_questionNum_askTime'),
				array('bqna.asktime'))
		->where("bqna.questionNum = $qNum");
		$allData = $db->fetchAll($select);
		return $allData;
	}
}	