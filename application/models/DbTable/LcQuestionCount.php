<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_DbTable_LcQuestionCount extends Zend_Db_Table_Abstract {

    // table name
    protected $_name = 'bal_lc_question_count';

    public function getAllData() {
        return $this->fetchAll();
    }

    public function checkQuestionCountExistance($question_count, $id) {
        $where = "question_count = '$question_count'";
        if (!empty($id) && $id != null) {
            $where .= "and id != '$id'";
        }
        return $this->fetchRow($where);
    }

    public function checkDurationExistance($duration, $id) {
        $where = "duration = '$duration'";
        if (!empty($id) && $id != null) {
            $where .= "and id != '$id'";
        }
        return $this->fetchRow($where);
    }

    public function updateData($id, $updateData) {
        $where = $this->_db->quoteInto("id = ?", $id);
        return $this->update($updateData, $where);
    }

    public function addData($addData) {
        return $this->insert($addData);
    }

    public function getData($id) {
        $where = "id = '$id'";
        return $this->fetchRow($where);
    }

    public function deleteData($id) {
        $where = "id = $id";
        return $this->delete($where);
    }

    public function markDefaultData($id) {
        $this->update(array('is_default' => 0));
        $where = $this->_db->quoteInto("id = ?", $id);

        $data = $this->getData($id);
        $defaultLC = new Application_Model_DbTable_DefaultLearningCustomization();
        $defaultLC->update(array('ask_question_after_every' => $data->duration, 'number_of_chances' => $data->question_count));

        return $this->update(array('is_default' => 1), $where);
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
    	->from(array('bqna' => 'bal_lc_question_count'),
    			array("bqna.question_count as questionNum"));
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
    	->from(array('bqna' => 'bal_lc_question_count'),
    			array("bqna.duration as asktime"));
    	$allData = $db->fetchAll($select);
    	return $allData;
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
    	->from(array('bqna' => 'bal_lc_question_count'),
    			array("bqna.duration as asktime"))
    			->where("bqna.question_count = $qNum");
    	$allData = $db->fetchAll($select);
    	return $allData;
    }

}
