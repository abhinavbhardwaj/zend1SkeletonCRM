<?php

/**
 * @author Akhilesh Nair <akhilesh.nair1@a3logics.in>
 */
class Application_Model_DbTable_UserRememberMeToken extends Zend_Db_Table_Abstract {

    // table name
    protected $_name = 'bal_user_remember_me_token';

    /**
     * Function to get user remember me data by user email
     * @param type $userEmail
     * @return token data
     */
    
    public function getDataByUser($userEmail) {
        
        $where = "user_email = '".$userEmail."'";
        return $this->fetchRow(array("user_email = ?"=>$userEmail));
        
    }
    
    /**
     * Function to add user remember me data
     * @param type $data array
     * @return inserted id
     */
    
    public function addUserData($data) {
        return $this->insert($data);
    }
    
    
    /**
     * Function to update user token data
     * @param type $data array and $userEmail
     * @return update id
     */
    public function updateUserTokenData($updateData, $userEmail) {
        return $this->update($updateData, array("user_email = ?"=>$userEmail));
    }
    
    /**
     * Function to get user email
     * @param type $userEmail
     * @return token data
     */
    
    public function authenticateToken($rememberCookie){
        $encryptInfo = explode("@",$rememberCookie);
                
        $query = $this->select();
        $query->from(array('r' => 'bal_user_remember_me_token'),"");
        $query->setIntegrityCheck(false);        
        $query->join(array('u' => 'bal_users'),'u.email = r.user_email');
        $query->where("user_token = ?",$rememberCookie)->orWhere("user_identifier = ?",$encryptInfo[0]);
        $userData = $this->fetchRow($query);
        
        /* UPDATE THE TOKEN TO A NEW ONE AND SET IT IN COOKIE */
        $tokenStr = md5(substr($userData['email'], 0, strpos($userData['email'], '@')));
        $addData['user_token'] = $encryptInfo[0].'@'.$tokenStr.time();
        $addData['updated_date'] = date("Y-m-d H:i:s");
        $result = $this->updateUserTokenData($addData,$userData['email']);
        if($result){
            setcookie(md5('remember_me'), $addData['user_token'], time() + (10 * 365 * 24 * 60 *60), "/");
        }
        //return user information from user table
        return $userData;
    }
    
    /**
     * Function to update user token data
     * @param type $data array and $userEmail
     * @return true or false
     */
    public function deleteUserTokenData($userEmail) {
        return $this->delete(array("user_email = ?"=>$userEmail));
    }
    
}
