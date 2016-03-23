<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Model_DbTable_LcLockDuration extends Zend_Db_Table_Abstract {

    // table name
    protected $_name = 'bal_lc_lock_duration';

    public function getAllData() {
        $query = $this->select();
        $query->order("duration ASC");
        return $this->fetchAll($query);
    }

    public function checkDurationExistance($duration, $id) {
        $where = "duration = '$duration'";
        if (!empty($id) && $id != null) {
            $where .= "and id != '$id'";
        }
        return $this->fetchRow($where);
    }

    public function updateData($id, $updateData) {
        $where = $this->_db->quoteInto("id = ?", $id);
        return $this->update($updateData, $where);
    }

    public function addData($addData) {
        return $this->insert($addData);
    }

    public function getData($id) {
        $where = "id = '$id'";
        return $this->fetchRow($where);
    }

    public function deleteData($id) {
        $where = "id = $id";
        return $this->delete($where);
    }

    public function markDefaultData($id) {
        $this->update(array('is_default' => 0));
        $where = $this->_db->quoteInto("id = ?", $id);

        $data = $this->getData($id);
        $defaultLC = new Application_Model_DbTable_DefaultLearningCustomization();
        $defaultLC->update(array('lock_device_for' => $data->duration));

        return $this->update(array('is_default' => 1), $where);
    }
    
    /** function to get all data
    * @param nill
    * @return ArrayIterator
    * created by suman on 24 March 2014
    */
    public function getAllLockDeviceTime()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('bldf' => 'bal_lc_lock_duration'),
    			array("bldf.duration as lock_device_for"));
    	$allData = $db->fetchAll($select);
    	return $allData;
    }
    
    /** function to get default lock device time
     * @param nill
     * @return ArrayObject
     * created by suman on 31 March 2014
     */
    public function getDefaultLockDeviceTime()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('bldf' => 'bal_lc_lock_duration'),
    			array("bldf.duration as lock_device_for"))
    	->where("bldf.is_default = 1");
    	$Data = $db->fetchRow($select);
    	return $Data;
    }

}
