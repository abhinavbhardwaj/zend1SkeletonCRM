<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_ParentalReward extends Zend_Loader_Autoloader {

    /**
     *
     * @var type 
     */
    protected $_db;

    /**
     * 
     */
    public function __construct() {
        $this->_db = new Application_Model_DbTable_ParentalReward();
    }

    public function save($data) {
        return $this->_db->insert($data);
    }

    public function update($data, $id) {
        return $this->_db->update($data, array('id = ?' => $id));
    }

    public function getListByChildBy($child_id, $where = NULL, $sOrder = NULL, $sortOr = NULL) {
        //echo $where; exit;
        $condition = array('child_id = ?' => $child_id);
        $orderBy = [];
        if(!empty($where)) {
            $condition[] = $where;
        }
        if(isset($sOrder) && $sOrder != '' && isset($sortOr) && $sortOr != '') {
            $orderBy[] = $sOrder . ' ' . $sortOr;
        }
        
        $data = $this->_db->fetchAll($condition, $orderBy);
        if ($data->count()) {
            return $data->toArray();
        }

        return FALSE;
    }

    public function getLatestRewardByChildId($child_id) {
        $data = $this->_db->fetchRow(array('child_id = ?' => $child_id), 'date_start DESC');
        if ($data) {
            return $data->toArray();
        }

        return false;
    }

    /**
     * 
     * @return Zend_Db_Table_Rowset
     */
    public function getInProgressRewards($child_id = NULL) {
        $where = array('is_achieved = 0');
        
        if(!empty($child_id)) {
            $where['child_id = ?'] = $child_id;
        }
        
        $data = $this->_db->fetchAll($where);
        if ($data->count()) {
            return $data;
        }

        return false;
    }

}
