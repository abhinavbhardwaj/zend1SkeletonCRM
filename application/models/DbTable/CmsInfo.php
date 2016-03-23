<?php

/*
 * This is a model class for Cms Content
 * Created By Sunil Khanchandani
 * August 13 2013 
 */

class Application_Model_DbTable_CmsInfo extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_pages';

    /*
     * this function is used to update page data 
     * @param page title
     * @param update data
     * 
     */

    public function updatePageData($pageId, $updateData) {

        $where = $this->_db->quoteInto("page_id = ?", $pageId);

        return $this->update($updateData, $where);
    }

    /*
     * This is a function to get page data 
     */

    public function getPageData($pageTitle) {
        $where = "alias = '$pageTitle'";
        return $this->fetchRow($where);
    }

}
