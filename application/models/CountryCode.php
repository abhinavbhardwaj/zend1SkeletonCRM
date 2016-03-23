<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_CountryCode extends Zend_Loader_Autoloader {

    /**
     *
     * @var type 
     */
    private $_tblCountryCode;

    /**
     * 
     */
    public function __construct() {
        $this->_tblCountryCode = new Application_Model_DbTable_CountryCode();
    }

    /**
     * 
     * @return type
     */
    public function getAllCountryCodes() {
        $codes = $this->_tblCountryCode->getAllCountryCodes();
        return $codes;
    }

    public function isValidCountryCode($code) {
        $row = $this->_tblCountryCode->getRowByCountryCode($code);
        if ($row) {
            return TRUE;
        }

        return FALSE;
    }

}
