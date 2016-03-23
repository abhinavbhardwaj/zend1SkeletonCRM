<?php

/*
 * This is a model class for Parent info
 * Created By Suman Khatri
 * Thursday, July 18 2013 
 */

class Application_Model_DbTable_ParentInfo extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_parents';

    //function is used to add parent info into the table
    public function addParent($parentInfo) {
        return $this->insert($parentInfo);
    }

    //function used to fetch parent info
    public function fetchUser($userId) {
        $where = "user_id = $userId";
        $usersData = $this->fetchRow($where);
        return $usersData;
    }

    /*
     * This is a function to fetch parent Data on basis of its Id
     * @param Filed to be fetched 
     * @param Parent Id
     */

    public function getParentdata($fetchDataArray, $parentId) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select();
        $select->from($this->_name);
        $select->join('bal_users', 'bal_users.user_id = bal_parents.user_id');
        $select->where("parent_id = ?", $parentId);

        if (!empty($fetchDataArray)) {
            $select->columns($fetchDataArray);
        }
        $fetchParentData = $db->fetchRow($select);
        return $fetchParentData;
    }

    //function used to update parent name
    public function updateParent($data, $userId) {
        $where = "user_id = $userId";
        return $this->update($data, $where);
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent username
     */

    public function isExistsParentData($userId) {

        $where = "user_id = '$userId' ";
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

    public function ParentData($userId) {

        $where = "user_id = '$userId' ";
        return $this->fetchRow($where);
    }

    /*
     * This is a  function to check parent already exist or not
     * @param parent username
     */

    public function isExistsParentDataWithParId($parentId, $dataType) {
        $where = $this->_db->quoteInto("parent_id = ?", $parentId);
        $parExist = $this->fetchRow($where);
        if ($parExist) {
            if ($dataType == 'arrayRow') {
                return $parExist;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

//function to get childs list on basis of grade and subject
    public function GetChildInfoByParentId($parentId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bp' => 'bal_parents'), array('parent_id'))
                ->joinLeft(array('bc' => 'bal_children'), 'bp.parent_id = bc.parent_id', array('bc.child_id'))
                ->joinLeft(array('bcd' => 'bal_child_devices'), 'bc.child_id = bcd.child_id', array('*'))
                ->where('bp.parent_id = ? ', $parentId)
                ->where("bcd.device_removed ='N'");
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    /*     * *******************function for geting parent user id**************
     * 
     */

    public function getParentUserId($chilId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bp' => 'bal_parents'), array('user_id'))
                ->joinLeft(array('bc' => 'bal_children'), 'bp.parent_id = bc.parent_id', array('bc.parent_id', 'bc.name', 'bc.gender'))
                ->where('bc.child_id = ? ', $chilId);
        $parentInfo = $db->fetchRow($select);
        return $parentInfo;
    }

    public function checkPhoneExistOrnot($childMobileNo) {
        $where = "phone_number = '$childMobileNo'";
        return $this->fetchRow($where);
    }

    public function checkPhoneExistOrnotonedit($childMobileNo, $parentId) {
        $where = "phone_number = '$childMobileNo' AND parent_id != $parentId";
        return $this->fetchRow($where);
    }

    public function checkPhoneExist($childMobileNo, $parentId) {
        $where = "phone_number = '$childMobileNo' and parent_id = $parentId";
        return $this->fetchRow($where);
    }

    public function getUserParentInfo($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bp' => 'bal_parents'), array('bp.parent_id', 'bp.first_name', 'bp.last_name', 'bp.phone_number', 'bp.parent_image', 'bp.display_name', 'bp.parent_type'))
                ->joinLeft(array('bu' => 'bal_users'), 'bp.user_id = bu.user_id', array('bu.user_id', 'bu.email'))
                ->where('bu.user_id = ? ', $userId);
        $parentInfo = $db->fetchRow($select);
        return $parentInfo;
    }

    public function getParentDataUsingChildId($chilId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bp' => 'bal_parents'), array('bp.*'))
                ->joinLeft(array('bc' => 'bal_children'), 'bp.parent_id = bc.parent_id', array('bc.parent_id'))
                ->where('bc.child_id = ? ', $chilId);
        $parentInfo = $db->fetchRow($select);
        return $parentInfo;
    }

    public function getParentDataUsingUniqueId($uniqueId) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select();
        $select->from(array('bp' => 'bal_parents'), array('bp.*'));
        $select->join(array('bc' => 'bal_children'), 'bp.parent_id = bc.parent_id', array('bc.parent_id'));
        $select->join(array('bcd' => 'bal_child_devices'), 'bcd.child_id = bc.child_id', '');
        $select->where('bcd.unique_key = ? ', $uniqueId);
        $parentInfo = $db->fetchRow($select);
        return $parentInfo;
    }

    public function getAllParentwithNoChild() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_children'), array('bl.parent_id'))
                ->having("COUNT(child_id) >= 1")
                ->group('bl.parent_id');
        $allData = $db->fetchAll($select);
        $allData = array_map('current', $allData);
        $allParentIds = implode(',', $allData);
        if (!empty($allParentIds) && $allParentIds != null) {
            $where = "bp.parent_id not in ($allParentIds)";
        } else {
            $where = "1";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('bp' => 'bal_parents'), array('bp.parent_id'))
                ->joinLeft(array('bu' => 'bal_users'), 'bp.user_id = bu.user_id', array())
                ->where("bu.email_verifiied = 'Y' and bu.user_id is not null")
                ->where($where)->where("bp.parent_id != 0")
                ->group('bp.parent_id');
        $parentInfo = $db->fetchAll($select);
        return $parentInfo;
    }

    /*     * ******************function for
     * geting total count of child************** */

    public function getTotalFinnyParent() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('this' => "bal_users"), array("sum(if(this.email_verifiied = 'Y',1,0)) AS totalParentEmailVerified",
                    "sum(if(this.email_verifiied = 'N',1,0)) AS totalParentEmailNottVerified"))
                ->where("this.user_id > 0");
        $result = $db->fetchRow($select);
        return $result;
    }
    
}