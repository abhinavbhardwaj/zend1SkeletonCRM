<?php

/*
 * This is a model class for Leader board overall
 * Created By Abhinav Bhardwaj
 * Monday, September 07 2015
 */

class Application_Model_DbTable_LeaderBoardOverAll extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_leader_board_overall';

    /*
     * this function is used insert data into table
     * @param array data 
     * created by abhinav
     * on September 07 2015
     */

    public function insertDataIntoLeaderBoard($data) {
        return $this->insert($data);
    }
    
    /*
     * this function is used insert data into table
     * @param array data 
     * created by abhinav
     * on September 07 2015
     */
    public function truncateTable() {
        $this->getAdapter()->query('TRUNCATE TABLE `' . $this->_name . '`');
        return $this;
    }    

}
