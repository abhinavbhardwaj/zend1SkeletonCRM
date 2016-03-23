<?php

/*
 * This is a model class for parent Device App Information
 * Created By Suman Khatri
 * Thursday, October 13 2014 
 */

class Application_Model_DbTable_ParentDeviceApps extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_parent_device_apps';

    /**
     * @desc this function is used to add  parent device app
     * @param data array
     * @author suman khatri on October 21 2014
     * @return last insert id
     */
    public function AddParentDeviceApps($data) {
        return $this->insert($data);
    }

    /**
     * @desc this function is used to add  parent device app
     * @param int $parentId parent id
     * @param int $deviceId device id
     * @author suman khatri on October 21 2014
     * @return arrayIterator 
     */
    public function getParentDeviceApps($parentId, $deviceId = NULL) {
        $where = $this->_db->quoteInto("parent_id = ?", $parentId);
        if (!empty($deviceId)) {
            $where .= $this->_db->quoteInto(" and device_id = ?", $deviceId);
        }
        return $this->fetchAll($where);
    }

    /**
     * @desc Function to delete apps for child and device
     * @param int $parentId parent id
     * @param int $deviceId device id
     * @author suman khatri on October 21 2014
     * @return result
     */
    public function removeDeviceAppsForParentAndDevice($deviceId)
    {
        $result = $this->delete(array("device_id = ?" => $deviceId)); //deletes record according to condition 
        return $result; //returns result
    }

    /*
     * this function is used to check device app existance
     * @param where condition
     * created by suman
     * on August 08 2013
     */

    public function CheckAppExistance($where) {
        $data = $this->fetchRow($where);
        if ($data) {
            return true;
        } else {
            return false;
        }
    }

}
