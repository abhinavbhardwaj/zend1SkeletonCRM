<?php

/*
 * This is a model class for Child Device relation Information
 * Created By Suman Khatri
 * Thursday, October 07 2014
 */

class Application_Model_DbTable_ChildDeviceRelationInfo extends Zend_Db_Table_Abstract
{

    // This is name of Table
    protected $_name = 'bal_child_device_relation';

    /**
     * @desc Function to check device is already exist or not in bal_child_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return array or null
     */
    public function checkDeviceExistOrNotInChildDeviceRelation($deviceId, $childId)
    {
        $where = $this->_db->quoteInto("bcdr.device_id = ?", $deviceId);
        if (!empty($childId) && $childId != null) {
            $where .= $this->_db->quoteInto(" and bcdr.child_id = ?", $childId);
        }
        $where .= $this->_db->quoteInto(" and bcdr.is_associated != ?", 0);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcdr' => 'bal_child_device_relation'), array('bcdr.*'))
                ->joinInner(array('bc' => 'bal_children'), 'bc.child_id = bcdr.child_id', array('bc.parent_id'))
                ->where($where);
        $deviceChildInfo = $db->fetchRow($select);
        return $deviceChildInfo;
    }

    /**
     * @desc Function to delete child device data in DB
     * @param $id
     * @author suman khatri on 8th October 2014
     * @return result
     */
    public function unAssociatePhone($id)
    {
        $data = array(
            'date_unassociation' => todayZendDate(),
            'is_associated' => 0);
        $where = $this->_db->quoteInto("id = ?", $id);
        return $this->update($data, $where); //update record matching where condition and return result
    }

    /**
     * @desc Function to add child device data in DB
     * @param array $insertData
     * @author suman khatri on 9th October 2014
     * @return last insertId
     */
    public function addChildDeviceInfo($insertData)
    {
        return $this->insert($insertData); //add data in DB and returns last insert id
    }

    public function associateDeviceWithChild($childId, $deviceId)
    {
        $isAlreadyAssociated = $this->fetchRow(array(
            'is_associated = ?' => '1',
            'child_id = ?' => $childId,
            'device_id = ?' => $deviceId
        ));
        if (!$isAlreadyAssociated) {

            $data = array(
                'is_associated' => 0,
                'date_unassociation' => new Zend_Db_Expr('NOW()')
            );
            $where = array(
                'device_id = ?' => $deviceId,
                'is_associated = ?' => '1'
            );
            $this->update($data, $where);
            $data = array(
                'child_id' => $childId,
                'device_id' => $deviceId,
                'date_association' => new Zend_Db_Expr('NOW()')
            );
            $this->insert($data);
        }

        return TRUE;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildDevices($childId, $parId = NULL)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.*'));
        $select->join(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.device_name'));
        $select->joinInner(array('bpdr' => 'bal_parent_device_relation'), 'bpdr.device_id = bdi.device_id', array(''));
        if (!empty($parId)) {
            $select->where("bpdr.parent_id = ?", $parId);
        }
        $select->where("bcgr.child_id = ?", $childId);
        $select->order("bcgr.is_associated DESC");
        $select->group("bdi.device_id");
        $deviceInfo = $db->fetchAll($select);
        return $deviceInfo;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildAssociateDevices($childId, $parId)
    {
        
        $where = $this->_db->quoteInto("bpdr.parent_id = ?", $parId);
        
        if($childId <> 0)
        $where .= $this->_db->quoteInto(" and bcgr.child_id = ?", $childId);
        
        $where .= $this->_db->quoteInto(" and bcgr.is_associated = ?", 1);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.*'))
                ->joinInner(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.*'))
                ->joinInner(array('bpdr' => 'bal_parent_device_relation'), 'bpdr.device_id = bdi.device_id', array(''))
                ->where($where)
                ->group("bdi.device_id");
        $deviceInfo = $db->fetchAll($select);
        return $deviceInfo;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildDeviceLockInfo($childId, $parId, $deviceId)
    {
        $where = $this->_db->quoteInto("bpdr.parent_id = ?", $parId);
        $where .= $this->_db->quoteInto(" and bcgr.child_id = ?", $childId);
        $where .= $this->_db->quoteInto(" and bcgr.device_id = ?", $deviceId);
        $where .= $this->_db->quoteInto(" and bcgr.is_associated = ?", 1);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.*'))
                ->joinInner(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.device_lock_status'))
                ->joinInner(array('bpdr' => 'bal_parent_device_relation'), 'bpdr.device_id = bdi.device_id', array(''))
                ->where($where)
                ->group("bdi.device_id");
        $deviceInfo = $db->fetchRow($select);
        return $deviceInfo;
    }

    public function getVirtualChildAssociateDevices($childId)
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.device_id', 'bcgr.is_associated'))
                ->join(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array(''))
                ->where("bcgr.child_id = ?", $childId)
                ->group("bdi.device_id");

        $deviceInfo = $db->fetchRow($select);
        return $deviceInfo;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildActiveDevices($childId, $parId, $deviceId = NULL)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select();
        $select->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.child_id'));
        $select->joinInner(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.*'));
        $select->joinInner(array('bpdr' => 'bal_parent_device_relation'), 'bpdr.device_id = bdi.device_id', array(''));
        $select->where("bpdr.parent_id = ?", $parId);
        $select->where("bcgr.child_id = ?", $childId);
        $select->where("bcgr.is_associated = 1");
        $select->where("bdi.device_removed = 'N'");
        $select->where("bdi.device_monitored = 'Y'");
        $select->where("(registered_id <> '' OR registered_id IS NOT NULL)");
        $select->where("device_lock_status = 'UNLOCK'");

        if (!empty($deviceId)) {
            $select->where("bdi.device_id = ?", $deviceId);
        }

        $select->group("bdi.device_id");
        $deviceInfo = $db->fetchAll($select);
        return $deviceInfo;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildAllDevices($childId)
    {
        $where = $this->_db->quoteInto("bcgr.child_id = ?", $childId);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.*'))
                ->joinInner(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.device_name'))
                ->where($where)
                ->order("bcgr.is_associated DESC")
                ->group("bdi.device_id");
        $deviceInfo = $db->fetchAll($select);
        return $deviceInfo;
    }

    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getChildActiveDevicesWithoutCurrentDevice($childId, $parId, $deviceId)
    {
        $where = $this->_db->quoteInto("bpdr.parent_id = ?", $parId);
        $where .= $this->_db->quoteInto(" and bcgr.child_id = ?", $childId);
        $where .= $this->_db->quoteInto(" and bcgr.is_associated = ?", 1);
        $where .= $this->_db->quoteInto(" and bdi.device_removed = ?", 'N');
        $where .= $this->_db->quoteInto(" and bdi.device_monitored = ?", 'Y');
        $where .= $this->_db->quoteInto(" and bcgr.device_id != ?", $deviceId);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bcgr' => 'bal_child_device_relation'), array('bcgr.child_id'))
                ->joinInner(array('bdi' => 'bal_device_info'), 'bdi.device_id = bcgr.device_id', array('bdi.*'))
                ->joinInner(array('bpdr' => 'bal_parent_device_relation'), 'bpdr.device_id = bdi.device_id', array(''))
                ->where($where)
                ->group("bdi.device_id");
        $deviceInfo = $db->fetchAll($select);
        return $deviceInfo;
    }

}