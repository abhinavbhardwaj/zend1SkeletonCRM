<?php

class Application_Model_Cms extends Zend_Loader_Autoloader {

    /**
     * @desc Function to get cms info for web 
     * @author suman khatri on 30th January 2014
     */
    //private data members
    private $_tblFaqInfo;
    private $_tblPagesInfo;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        $this->_tblFaqInfo = new Application_Model_DbTable_FaqInfo ();
        $this->_tblPagesInfo = new Application_Model_DbTable_CmsInfo ();
    }

    /**
     * @desc Function to get all FAQ
     * @param nill
     * @author suman khatri on 30th January 2014
     * @return ArrayIterator
     */
    public function getAllFaqData() {
        $faqData = $this->_tblFaqInfo->getAllFAQ(); //getting all faqs
        return $faqData; //returns ArrayIterator
    }

    /**
     * @desc Function to get FAQ data
     * @param id
     * @author suman khatri on 31st January 2014
     * @return ArrayObject
     */
    public function getFaqData($faqId) {
        $faqData = $this->_tblFaqInfo->getFAQData($faqId); //getting faq data
        return $faqData; //returns ArrayIterator
    }

    /**
     * @desc Function to update FAQ data
     * @param $faqId,$updateData
     * @author suman khatri on 31st January 2014
     * @return result
     */
    public function updateFaqData($faqId, $updateData) {
        $result = $this->_tblFaqInfo->updateFAQData($faqId, $updateData); //updates faq data
        return $result; //returns $result
    }

    /**
     * @desc Function to add FAQ data
     * @param $updateData
     * @author suman khatri on 31st January 2014
     * @return result
     */
    public function addFaqData($addData) {
        $result = $this->_tblFaqInfo->insertFAQData($addData); //add faq data
        return $result; //returns $result
    }

    /**
     * @desc Function to check duplicacy of question of FAQ
     * @param $updateData,$faqIdEdit
     * @author suman khatri on 31st January 2014
     * @return result
     */
    public function checkExistanceofQuestion($quesTion, $faqIdEdit) {
        $qData = $this->_tblFaqInfo->checkExistanceofQuestion($quesTion, $faqIdEdit); //getting data
        if (!empty($qData) && $qData != null) {
            return true; //return true if record exist
        } else {
            return false; //return false if record doesn't exist
        }
    }

    /**
     * @desc Function to check duplicacy of sort order of FAQ
     * @param $sortOrder,$faqIdEdit
     * @author suman khatri on 31st January 2014
     * @return result
     */
    public function checkSortOrderExistance($sortOrder, $faqIdEdit) {
        $sData = $this->_tblFaqInfo->checkSortOrderExistance($sortOrder, $faqIdEdit); //getting data
        if (!empty($sData) && $sData != null) {
            return true; //return true if record exist
        } else {
            return false; //return false if record doesn't exist
        }
    }

    /**
     * @desc Function to remove FAQ
     * @param $faqId
     * @author suman khatri on 31st January 2014
     * @return result
     */
    public function deleteFAQData($faqId) {
        $sData = $this->_tblFaqInfo->deleteFAQData($faqId); //delete data
        return $sData;
    }

    public function getPageData($pageTitle) {
        $getAboutUsData = $this->_tblPagesInfo->getPageData($pageTitle);
        return $getAboutUsData;
    }

}
