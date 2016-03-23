<?php

/**
 * @author Akhilesh Nair <akhilesh.nair1@a3logics.in>
 */
class Application_Model_UserRememberMeToken extends Zend_Loader_Autoloader {

    /**
     *
     * @var type 
     */
    private $_tblUserRememberMeToken;

    /**
     * 
     */
    public function __construct() {
        $this->_tblUserRememberMeToken = new Application_Model_DbTable_UserRememberMeToken();
    }

    /**
     * 
     * @return type
     */
    public function getUserTokenData($userEmail){
        return $this->_tblUserRememberMeToken->getDataByUser($userEmail);
    }

    /*
    * Function to save User remember me data into table
    */
    public function saveUserTokenData($addData) {        
        return $this->_tblUserRememberMeToken->addUserData($addData);
    }
    
    /*
    * Function to update User token into table
    */
    public function updateUserTokenData($updatedData, $userEmail){   
        return $this->_tblUserRememberMeToken->updateUserTokenData($updatedData, $userEmail);
    }
    
    /**
     * Function to return user email
     * @param type $userIdentifier
     * @return type
     */
    public function authenticateToken($rememberCookie){
        return $this->_tblUserRememberMeToken->authenticateToken($rememberCookie);
    }
    
    /**
     * Function to delete user token data
     * @param type $userIdentifier
     * @return type
     */
    public function deleteUserTokenData($userEmail){
        return $this->_tblUserRememberMeToken->deleteUserTokenData($userEmail);
    }


}
