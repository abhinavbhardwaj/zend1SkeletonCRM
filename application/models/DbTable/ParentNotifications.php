<?php

/*
 * This is a model class for notifications to parent regarding App log Information
 * Created By Suman Khatri
 * Thursday, August 09 2013 
 */

class Application_Model_DbTable_ParentNotifications extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_parent_notification';

    /*
     * this function is used to add device app log
     * @param data and device key and device log
     * created by suman
     * on August 07 2013
     */

    public function AddParentNotification($data) {
        return $this->insert($data);
    }

    public function getAllNotification($userId) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_parent_notification'), array('child_device_id', 'created_date', 'description', 'notification_type', 'notification_id', 'childe_name', 'child_id'))
                ->joinLeft(array('blcdr' => 'bal_child_device_relation'), 'bl.child_device_id  = blcdr.device_id and bl.child_id = blcdr.child_id', array('blcdr.device_id'))
                ->joinLeft(array('chdinfo' => 'bal_children'), 'bl.child_id = chdinfo.child_id', array('chdinfo.name', 'chdinfo.firstname', 'chdinfo.lastname', 'chdinfo.image', 'chdinfo.gender'))
                ->where("bl.user_id = $userId")
                ->where("bl.created_date >= (now() - interval 7 day)")
                ->order('bl.created_date desc');

        $select->group('notification_id');
        $notificationInfo = $db->fetchAll($select);
        return $notificationInfo;
    }

    public function getUnreadNotification($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_parent_notification'), array('bl.*'))
                ->where('bl.user_id = ? ', $userId)
                ->where("bl.created_date >= (now() - interval 7 day)")
                ->where("bl.seen_by_user ='N'");
        $select->group('notification_id');
        $unreadNotificationInfo = $db->fetchAll($select);
        return $unreadNotificationInfo;
    }

    public function updateNotificationData($userId, $updateData) {

        $where = $this->_db->quoteInto("user_id = ?", $userId);
        $updateNotificationInfo = $this->update($updateData, $where);
        return $updateNotificationInfo;
    }

    public function extractAllNotification($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_parent_notification'), array('created_date', 'description', 'notification_type', 'childe_name'))
                ->joinLeft(array('blcdr' => 'bal_child_device_relation'), 'bl.child_device_id  = blcdr.device_id and bl.child_id = blcdr.child_id', array('blcdr.device_id', 'blcdr.child_id'))
                ->joinLeft(array('chdinfo' => 'bal_children'), 'bl.child_id = chdinfo.child_id', array('chdinfo.name', 'chdinfo.image'))
                ->where('bl.user_id = ? ', $userId)
                ->order('bl.created_date desc');
        $select->group('notification_id');
        $notificationInfo = $db->fetchAll($select);
        return $notificationInfo;
    }

    /**
     * function to get all notification of today of a parent
     * 
     * @param
     *        	$userId,$date,$childId
     * @return 
     * 			Array 
     * @author 
     * 			Suman Khatri on 24 Nov. 2013
     */
    public function extractAllFilteredNotification($userId, $date, $childId) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_parent_notification'), array('bl.*'))
                ->joinLeft(array('chdinfo' => 'bal_children'), 'bl.child_id = chdinfo.child_id', array('chdinfo.name', 'chdinfo.firstname', 'chdinfo.lastname', 'chdinfo.image', 'chdinfo.gender'))
                ->joinLeft(array('blcdr' => 'bal_child_device_relation'), 'bl.child_device_id  = blcdr.device_id and bl.child_id = blcdr.child_id', array('blcdr.device_id', 'blcdr.child_id'))
                ->where('bl.user_id = ? ', $userId)
                ->order('bl.created_date desc');

        if (!empty($childId) && $childId != 'all') {
            $select->where("bl.child_id = $childId");
        }
        if ($date == 'today') {
            $select->where("bl.created_date >= (now() - interval 1 day)");
        }

        $select->group('notification_id');
        $notificationInfo = $db->fetchAll($select);
        return $notificationInfo;
    }
    
    /**
     * function to get all notification of today of a parent
     * 
     * @param
     *        	$userId,$date,$childId
     * @return 
     * 			Array 
     * @author 
     * 			Suman Khatri on 24 Nov. 2013
     */
    public function getAllNotifications($userId, $childId = null, $topPopover, $limitRec, $offset) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        
        $selectAry = array('bl.*');
        
        if ($topPopover == 1) {
            
            $countUnreadQ = $db->select();
            $countUnreadQ->from(array('bln' => 'bal_parent_notification'), array('count(bln.notification_id) as unreadcount'));
            $countUnreadQ->where('bln.seen_by_user="N"');
            $countUnreadQ->where('bln.user_id="'.$userId.'"');
            //$countUnreadQ->where("bln.created_date BETWEEN now() - INTERVAL ".$noOfDays." DAY AND now()");
            $selectAry['unreadnotifications'] = new Zend_Db_Expr('('.$countUnreadQ.')');
        }

        $select->from(array('bl' => 'bal_parent_notification'), $selectAry);
        
        $select->joinLeft(
                array('chdinfo' => 'bal_children'), 
                'bl.child_id = chdinfo.child_id', 
                array(
                    'chdinfo.name',
                    'chdinfo.firstname',
                    'chdinfo.lastname',
                    'chdinfo.image',
                    'chdinfo.gender'
                    )
                );
        
        $select->joinLeft(
                array('blcdr' => 'bal_child_device_relation'),
                'bl.child_device_id  = blcdr.device_id and bl.child_id = blcdr.child_id',
                array('blcdr.device_id', 'blcdr.child_id')
                );
        
        $select->where('bl.user_id = ? ', $userId);
        //$select->where("bl.created_date BETWEEN now() - INTERVAL ".$noOfDays." DAY AND now()");

        /* condition to get notifications of the selected child */
        if (isset($childId) && $childId != '') {
            $select->where('bl.child_id = ? ', $childId);
        }
        
        $select->order('bl.created_date desc');
        
        $select->group('notification_id');
        
        if(isset($limitRec) && $limitRec != '') {
            $select->limit($limitRec, $offset);
        }
        //echo $select; exit;
        return $db->fetchAll($select);
    }

    /**
     * function to get notification of lock device 
     * 
     * @param
     *        	int deviceId
     * @return 
     * 			Array 
     * @author 
     * 			Suman Khatri
     */
    public function GetNotificationForDevice($deviceId, $childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_parent_notification'), array('bl.child_device_id', 'bl.created_date', 'bl.description', 'bl.notification_type', 'bl.childe_name', 'bl.notification_id'))
                ->joinLeft(array('blns' => 'bal_parent_notification_status'), 'blns.notification_id  = bl.notification_id', array('blns.status', 'blns.id'))
                //->where("blns.status != 'Y'")
                ->where('bl.child_device_id = ? ', $deviceId)
                ->where("bl.child_id= ? ", $childId)
                ->where("bl.notification_type = 'LOCK'")
                ->order('bl.created_date desc');
        $select->limit(1);
        $notificationInfo = $db->fetchRow($select);
        return $notificationInfo;
    }

}
