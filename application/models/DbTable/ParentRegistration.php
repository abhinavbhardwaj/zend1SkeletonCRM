<?php

/*
 * This is a model class for Parent Registration
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */

class Application_Model_DbTable_ParentRegistration extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_users';

    /*
     * This is a function to register user
     * @param array
     * @return Last insert Id
     */

    public function addUserData($data) {
        $addUser = $this->insert($data);
        if ($addUser) {
            return $this->_db->lastInsertId();
        }
    }

// end of function addUserData

    /*
     * This is a  function to check parent already exist or not
     * @param parent email
     */

    public function isExistsEmail($email) {

        $where = "email = '$email' ";
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent username
     */

    public function isExistsUsername($username) {

        $where = "user_name = '$username' ";
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent parentId
     */

    public function isExistsParent($parentId) {

        $where = $this->_db->quoteInto("user_id=?", $parentId);
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This is a function to verify Mail;
     */

    public function verifyMail($parId, $verficationCode) {
        $where = $this->_db->quoteInto("user_id=?", $parId);
        $where .= $this->_db->quoteInto(" AND verification_code =?", $verficationCode);
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            $statusData = array('email_verifiied' => 'Y');
            $updateStatus = $this->update($statusData, $where);
            if ($updateStatus) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * This is a function to update user info
     */

    public function updateUserInfo($updateData, $userId) {
        $where = $this->_db->quoteInto("user_id=?", $userId);
        $updateInfo = $this->update($updateData, $where);
        return $updateInfo;
    }

//function used to fetch password of the user
    public function fetchDetail($userId) {
        $where = "user_id = $userId";
        return $this->fetchRow($where);
    }

//function used to fetch user de of the user
    public function fetchUserByDetailByEmail($userEmail) {
        $where = $this->_db->quoteInto("email = ?", $userEmail);
        return $this->fetchRow($where);
    }

    //function used to update user name
    public function updateUserName($data, $userId) {
        $where = "user_id = $userId";
        return $this->update($data, $where);
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent username
     */

    public function isExistsUsernameusingId($username, $userId) {

        $where = "user_name = '$username' and user_id != $userId";
        $userExist = $this->fetchRow($where);
        if ($userExist) {
            return true;
        } else {
            return false;
        }
    }

    public function isverifiedEmail($verficationCode, $parId) {
        $where = "user_id = $parId and verification_code = $verficationCode and email_verifiied = 'Y'";
        $isverifiedExist = $this->fetchRow($where);
        if ($isverifiedExist) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function to fetch parent list 
     * @param nill
     * @return Array List
     * @author Suman khatri
     */
    public function GetAllParent() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bu' => 'bal_users'), array("bu.email", "bu.is_active", "bu.user_id"))
                ->joinLeft(array('bp' => 'bal_parents'), 'bu.user_id = bp.user_id', array('bp.first_name', 'bp.middle_name', 'bp.last_name', 'bp.phone_number', 'bp.parent_type', 'bp.parent_id', 'bp.created_date'))
                ->joinLeft(array('bc' => 'bal_children'), 'bc.parent_id = bp.parent_id', array("SUM(if(bc.parent_id = bp.parent_id,1,0)) as TotalChild"))
                ->where("bp.user_id != 0")
                ->group("bu.user_id")
                ->order(array("bp.created_date desc"));
        $ParentData = $db->fetchAll($select);
        return $ParentData;
    }

    /**
     * function to fetch parent list according to search
     * @param varchar searchData
     * @return Array List
     * @author Suman khatri
     */
    public function GetSearchedParent($searchData) {

        if (strtolower($searchData) == 'male') {
            $search = "D";
        } else if (strtolower($searchData) == 'female') {
            $search = "M";
        }
        if (!empty($search)) {
            $where = "bp.parent_type like '%$search%'";
        } else {
            $where = "CONCAT(bp.first_name,bp.last_name) like '%$searchData%' or bu.email like '%$searchData%' or
			bp.first_name like '%$searchData%' or bp.middle_name like '%$searchData%' or bp.last_name like '%$searchData%' 
			or bp.phone_number like '%$searchData%'";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bu' => 'bal_users'), array("bu.email", "bu.is_active", "bu.user_id"))
                ->joinLeft(array('bp' => 'bal_parents'), 'bu.user_id = bp.user_id', array('bp.first_name', 'bp.middle_name', 'bp.last_name', 'bp.phone_number', 'bp.parent_type', 'bp.parent_id', 'bp.created_date'))
                ->joinLeft(array('bc' => 'bal_children'), 'bc.parent_id = bp.parent_id', array("SUM(if(bc.parent_id = bp.parent_id,1,0)) as TotalChild"))
                ->where($where)->where("bp.user_id != 0")
                ->group("bu.user_id")
                ->order(array("bp.created_date desc"));
        //echo $select;die;
        $ParentData = $db->fetchAll($select);
        return $ParentData;
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent email,$userId
     */

    public function isExistsEmailUsingUserId($email, $userId) {

        $where = "email = '$email' and user_id != $userId";
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check parent email is verified or not
     * @param string $emailId emai id
     * @param string $passWord password
     * 
     * @author suman khatri on October 22 2014
     * @return true / flase
     */
    public function checkUserEmailVerification($emailId, $passWord) {
        $where = $this->_db->quoteInto("email = ?",$emailId); 
        $where .= $this->_db->quoteInto(" and password = ?",  md5($passWord)); 
        $parExist = $this->fetchRow($where);
        if (!empty($parExist) && $parExist != null) {
            if($parExist['email_verifiied'] == 'N' 
                || $parExist['email_verifiied'] == null
            ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /** *************
     * function for delete Parent info
     * @param Int parentId
     * return int
     *
     * ************************ */

    public function deleteData($parentId, $parentEmail = null) {
        
        if(!empty($parentEmail)){
            $where = $this->_db->quoteInto("email = ?", $parentEmail);
        } else {
            $where = $this->_db->quoteInto("user_id = ?", $parentId);
        }
        return $this->delete($where);
    }
}

// end of class Application_Model_DbTable_ParentRegistration