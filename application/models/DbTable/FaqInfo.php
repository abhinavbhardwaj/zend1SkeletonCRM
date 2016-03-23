<?php

/**
 * This is a model class for FAQ Content
 * created by suman on 30 january 2014
 */
class Application_Model_DbTable_FaqInfo extends Zend_Db_Table_Abstract
{

    // This is the name of Table
    protected $_name = 'bal_faq';

    /**
     * function to update faq data
     * @param int $faqId,array $updateData
     * @return no of updated rows
     * created by suman on 30 january 2014
     */
    public function updateFAQData($faqId, $updateData)
    {
        $data = array(
            'question' => $updateData['question'],
            'answer' => $updateData['answer'],
            'sort_order' => $updateData['sort_order']
        );
        return $this->update($data, array('id = ?' => $faqId));
    }

    /**
     * function to get faq data
     * @param int $faqId
     * @return ArrayObject
     * created by suman on 30 january 2014
     */
    public function getFAQData($faqId)
    {
        return $this->fetchRow(array('id = ?' => $faqId));
    }

    /**
     * function to get all faq data
     * @param nill
     * @return ArrayIterator
     * created by suman on 30 january 2014
     */
    public function getAllFAQ()
    {
        return $this->fetchAll(null, 'sort_order');
    }

    /**
     * function to insert faq data
     * @param $insertData
     * @return id of inserted row
     * created by suman on 30 january 2014
     */
    public function insertFAQData($insertData)
    {
        $data = array(
            'question' => $insertData['question'],
            'answer' => $insertData['answer'],
            'sort_order' => $insertData['sort_order']
        );
        return $this->insert($data);
    }

    /**
     * function to remove faq data
     * @param $faqId
     * @return 
     * created by suman on 31 january 2014
     */
    public function deleteFAQData($faqId)
    {
        return $this->delete(array('id = ?' => $faqId));
    }

    /**
     * function to check existance of question of faq 
     * @param $quesTion,$faqIdEdit
     * @return ArrayObject
     * created by suman on 31 january 2014
     */
    public function checkExistanceofQuestion($quesTion, $faqIdEdit)
    {
        $where = array("question = ?" => $quesTion);
        if (!empty($faqIdEdit)) {
            $where['id <> ?'] = $faqIdEdit;
        }
        return $this->fetchRow($where);
    }

    /**
     * function to check existance of sort order of faq 
     * @param $sortOrder,$faqIdEdit
     * @return 
     * created by suman on 31 january 2014
     */
    public function checkSortOrderExistance($sortOrder, $faqIdEdit)
    {
        $where = array("sort_order = ?" => $sortOrder);
        if (!empty($faqIdEdit)) {
            $where['id <> ?'] = $faqIdEdit;
        }
        return $this->fetchRow($where);
    }

}
