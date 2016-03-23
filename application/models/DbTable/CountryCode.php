<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_DbTable_CountryCode extends Zend_Db_Table_Abstract {

    // table name
    protected $_name = 'bal_countrycode';

    public function getAllCountryCodes() {
        $query = $this->select();
        $query->order("country");
        return $this->fetchAll($query);
    }

    public function getRowByCountryCode($code) {
        return $this->fetchRow(array('code = ?' => $code));
    }
    
}
