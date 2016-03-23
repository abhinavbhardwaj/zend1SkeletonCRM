<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_Timezone extends Zend_Loader_Autoloader {

    /**
     *
     * @var type 
     */
    private $_tblTimezone;

    /**
     * 
     */
    public function __construct() {
        $this->_tblTimezone = new Application_Model_DbTable_Timezone();
    }

    /**
     * 
     * @return type
     */
    public function getAllTimezone() {
        return $this->_tblTimezone->getAllTimezone();
    }

    /**
     * 
     * @param type $parent_id
     * @return type
     */
    public function getUserTimezone($user_id = NULL) {
        return $this->_tblTimezone->getUserTimezone($user_id);
    }

}
