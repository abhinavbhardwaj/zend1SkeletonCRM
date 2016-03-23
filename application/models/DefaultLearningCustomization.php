<?php

class Application_Model_DefaultLearningCustomization extends Zend_Loader_Autoloader {

    /**
     * defined all object variables that are used in entire class
     */
    private $_DefaultLC;
    private $_lcQuestionCount;
    private $_lcLockDuration;

    /**
     * function for create all model table object used this object to call model table functions
     */
    public function __construct() {
        //creates object for model file ChildDeviceInfo
        $this->_DefaultLC = new Application_Model_DbTable_DefaultLearningCustomization();
        //creates object for model file ParentInfo

        $this->_lcQuestionCount = new Application_Model_DbTable_LcQuestionCount();
        $this->_lcLockDuration = new Application_Model_DbTable_LcLockDuration();
    }

    /**
     * function for geting default values of learnig customization
     */
    public function getLCData() {
        return $this->_DefaultLC->getLCData();
    }

    public function getAllLcQuestionCount() {
        $lcData = $this->_lcQuestionCount->getAllData();

        return $lcData;
    }

    public function checkQuestionCountExistance($question_count, $id) {
        $qData = $this->_lcQuestionCount->checkQuestionCountExistance($question_count, $id); //getting data
        if (!empty($qData) && $qData != null) {
            return true; //return true if record exist
        } else {
            return false; //return false if record doesn't exist
        }
    }

    public function checkDurationExistance($duration, $id) {
        $qData = $this->_lcQuestionCount->checkDurationExistance($duration, $id); //getting data
        if (!empty($qData) && $qData != null) {
            return true; //return true if record exist
        } else {
            return false; //return false if record doesn't exist
        }
    }

    public function updateLcQuestionCountData($id, $data) {
        $result = $this->_lcQuestionCount->updateData($id, $data); //updates faq data
        return $result; //returns $result
    }

    public function addLcQuestionCountData($data) {
        $result = $this->_lcQuestionCount->addData($data); //add faq data
        return $result; //returns $result
    }

    public function getLcQuestionCountData($id) {
        $data = $this->_lcQuestionCount->getData($id); //getting faq data
		return $data; //returns ArrayIterator
    }
    
    public function deleteLcQuestionCountData($id) {
        $sData = $this->_lcQuestionCount->deleteData($id); //delete data
		return $sData;
    }
    
    public function markDefaultLcQuestionCountData($id) {
        $sData = $this->_lcQuestionCount->markDefaultData($id); //delete data
		return $sData;
    }
    
    public function getAllLcLockDuration() {
        $lcData = $this->_lcLockDuration->getAllData();

        return $lcData;
    }

    public function checkLockDurationExistance($duration, $id) {
        $qData = $this->_lcLockDuration->checkDurationExistance($duration, $id); //getting data
        if (!empty($qData) && $qData != null) {
            return true; //return true if record exist
        } else {
            return false; //return false if record doesn't exist
        }
    }

    public function updateLcLockDurationData($id, $data) {
        $result = $this->_lcLockDuration->updateData($id, $data); //updates faq data
        return $result; //returns $result
    }

    public function addLcLockDurationData($data) {
        $result = $this->_lcLockDuration->addData($data); //add faq data
        return $result; //returns $result
    }

    public function getLcLockDurationData($id) {
        $data = $this->_lcLockDuration->getData($id); //getting faq data
		return $data; //returns ArrayIterator
    }
    
    public function deleteLcLockDurationData($id) {
        $sData = $this->_lcLockDuration->deleteData($id); //delete data
		return $sData;
    }
    
    public function markDefaultLcLockDurationData($id) {
        $sData = $this->_lcLockDuration->markDefaultData($id); //delete data
		return $sData;
    }
}
