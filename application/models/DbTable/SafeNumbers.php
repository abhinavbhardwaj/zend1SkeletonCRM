<?php

/*
 * This is a model class for Safe Numbers Information
 * Created By Sunil Khanchandani 
 * 4 September 2013 
 */

class Application_Model_DbTable_SafeNumbers extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_parent_safe_number';

    //function to add standards
    public function fetchSafeNumberData($parentId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $db->quoteInto("parent_id =?", $parentId);
        $select = $db->select()
                ->from($this->_name)
                ->where($where)
                ->order('created_date asc');
        $safeNumberDetails = $db->fetchAll($select);
        return $safeNumberDetails;
    }

    public function existTitle($title, $parentId, $safeeditId) {
        $where = $this->_db->quoteInto("title = ?", $title);
        $where .= $this->_db->quoteInto(" and parent_id = ?", $parentId);
        if (isset($safeeditId) && !empty($safeeditId)) {
            $where .= $this->_db->quoteInto(" AND safe_number_id <> ?", $safeeditId);
        }
        $checkTitle = $this->fetchRow($where);
        if ($checkTitle) {
            return true;
        } else {
            return false;
        }
    }

    public function existPhone($phoneNumber, $countryCode, $parentId, $safeNumberId) {

        $where = "number = '$phoneNumber' AND country_code = '$countryCode'";
        if (!empty($parentId)) {
            $where .= " AND parent_id = '$parentId'";
        }
        if (isset($safeNumberId) && !empty($safeNumberId)) {
            $where .= " AND safe_number_id <> '$safeNumberId'";
        }
        
        $checkPhone = $this->fetchRow($where);
        if ($checkPhone) {
            return true;
        } else {
            return false;
        }
    }

    public function existTitleById($title, $safeNumberId) {
        $where = $this->_db->quoteInto("title = ?", $title);
        $where .= $this->_db->quoteInto(" and safe_number_id = ?", $safeNumberId);
        $checkTitle = $this->fetchRow($where);
        if ($checkTitle) {
            return true;
        } else {
            return false;
        }
    }

    public function existPhoneById($phoneNumber, $safeNumberId) {

        $where = "number = '$phoneNumber'";
        $where .= " AND safe_number_id != '$safeNumberId'";
        $checkPhone = $this->fetchRow($where);
        if ($checkPhone) {
            return true;
        } else {
            return false;
        }
    }

    //function used to add safe Number
    public function addSafeNumber($data) {
        $addSafeNumber = $this->insert($data);
        return $addSafeNumber;
    }

//function used to add safe Number
    public function removeSafeNumber($safeNumberId) {
        $where = $this->_db->quoteInto("safe_number_id = ?", $safeNumberId);
        $removeSafeNumber = $this->delete($where);
        return $removeSafeNumber;
    }

    //function used to removes safe Numbers that are parent not saved into db
    public function removeSafeNumberLists($parentId) {

        /* get prives number************** */
        $this->updateSafeNumberListEdited($parentId);
        $data = array('deleted_safe_number' => 'N');
        $result = $this->updateSafeNumberDataParent($data, $parentId);
        $where = $this->_db->quoteInto("parent_id = ?", $parentId);
        $where .=" AND save_list = 'N'";
        $removeSafeNumber = $this->delete($where);
        return $removeSafeNumber;
    }

//function used to fetch a row of safe Number
    public function fetchRowSafeNumber($safeNumberId) {
        $where = "safe_number_id = '$safeNumberId'";
        $data = $this->fetchRow($where);
        return $data;
    }

//function used to update safe Number Data using parentId
    public function updateSafeNumberDataParent($data, $perentId) {
        $where = $this->_db->quoteInto("parent_id = ?", $perentId);
        $updateSafeNumberData = $this->update($data, $where);
        return $updateSafeNumberData;
    }

    //function used to update safe Number Data using parentId
    public function removeSafeNumberS($perentId) {
        $where = $this->_db->quoteInto("parent_id = ?", $perentId);
        $where .= "AND deleted_safe_number = 'Y'";
        $updateSafeNumberData = $this->delete($where);
        return $updateSafeNumberData;
    }

//function used to update safe Number Data
    public function updateSafeNumberData($data, $safeNumberId) {
        $where = $this->_db->quoteInto("safe_number_id = ?", $safeNumberId);
        $updateSafeNumberData = $this->update($data, $where);
        return $updateSafeNumberData;
    }

    public function updateSafeNumberList($parentId) {
        $this->removeSafeNumberS($parentId);
        $this->saveEditedNumbers($parentId);
        $where = "parent_id = '$parentId'";
        $data = array('save_list' => 'Y');
        return $this->update($data, $where);
    }

    public function saveEditedNumbers($parentId) {
        $data = array('edited_safe_number' => 'N');
        $where = "parent_id = '$parentId'";
        return $this->update($data, $where);
    }

    public function updateSafeNumberListEdited($parentId) {
        $where = "edited_safe_number = 'Y'";
        $data = $this->fetchSafeNumberData($parentId);
        if (!empty($data)) {
            foreach ($data as $dataA) {
                if (!empty($dataA[edited_safe_number]) && $dataA[edited_safe_number] == 'Y') {
                    $safeNumberId = $dataA['safe_number_id'];
                    $dataresult = array('number' => $dataA['edited_number'],
                        'edited_safe_number' => 'N',
                        'title' => $dataA['edited_name'],
                        'edited_number' => '',
                        'edited_safe_name' => 'N',
                        'edited_name' => '');
                    $this->updateSafeNumberData($dataresult, $safeNumberId);
                }
            }
        }
    }

}
