<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_DbTable_Timezone extends Zend_Db_Table_Abstract {

    // table name
    protected $_name = 'bal_timezone';
    protected $_defaultTimezone = array('value_php' => 'America/Los_Angeles', 'value_mysql' => 'America/Los_Angeles');

    public function getAllTimezone() {
        $query = $this->select();
        $query->order("FIELD(value_php, 'America/Los_Angeles') DESC");
        return $this->fetchAll($query);
    }

    /**
     * 
     * @param type $parent_id
     * @return type
     */
    public function getUserTimezone($user_id = NULL) {
        if ($user_id !== NULL) {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from($this);
            $query->join('bal_parents', 'bal_parents.timezone_id = bal_timezone.timezone_id', '');
            $query->where('bal_parents.user_id = ?', $user_id);
            $result = $this->fetchRow($query);

            if ($result && count($result)) {
                $timezone = $result->toArray();
            }
        }

        if (!isset($timezone)) {
            $timezone = $this->_defaultTimezone;
        }

        return $timezone;
    }

}
