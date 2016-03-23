<?php

class Application_Model_Notification extends Zend_Loader_Autoloader
{
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblNotification;
    private $_tblNotificationStatus;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct()
    {
        //creates object for model file ParentNotifications
        $this->_tblNotification = new Application_Model_DbTable_ParentNotifications();
        //creates object for model file ParentNotificationStatus
        $this->_tblNotificationStatus = new Application_Model_DbTable_ParentNotificationStatus();
    }

    /**
     * @desc Function to get notification for device 
     * @param $deviceId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getNotificationForDevie($deviceId, $childId)
    {
        $deviceNoti = $this->_tblNotification->GetNotificationForDevice($deviceId, $childId);
        return $deviceNoti; //returns array
    }

    /**
     * @desc Function to get all notification of parent 
     * @param $userId
     * @author suman khatri on 20th November 2013
     * @return ArrayIterator
     */
    public function getAllNotificationForUser($userId)
    {
        $notiData = $this->_tblNotification->getAllNotification($userId); //get all notification
        return $notiData; //returns array
    }

    /**
     * @desc Function to get unread notification of parent 
     * @param $userId
     * @author suman khatri on 20th November 2013
     * @return ArrayIterator
     */
    public function getAllUnreadNotificationForUser($userId)
    {
        $notiData = $this->_tblNotification->getUnreadNotification($userId); //get all notification
        return $notiData; //returns array
    }

    /**
     * @desc Function to get notification status
     * @param $notiId
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function getNotificationStatus($notiId)
    {
        //getting notification status
        $result = $this->_tblNotificationStatus->getNotificationStatus($notiId);
        return $result; //returns result
    }

    /**
     * @desc Function to update notification to read from unread
     * @param $userId
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function updateNotificationToRead($userId)
    {
        $updateDatarray = array('seen_by_user' => 'Y');
        $updateNotiFicationData = $this->_tblNotification->updateNotificationData($userId, $updateDatarray);
        return $updateNotiFicationData; //returns result
    }

    /**
     * @desc Function to get all notification of parent 
     * @param $userId
     * @author suman khatri on 20th November 2013
     * @return ArrayIterator
     */
    public function getAllNotificationForNotificationList($userId)
    {
        $notiData = $this->_tblNotification->extractAllNotification($userId); //get all notification
        return $notiData; //returns array
    }

    /**
     * @desc Function to get all notification of parent of today
     * @param $userId,$day,$childId
     * @author suman khatri on 24th November 2013
     * @return ArrayIterator
     */
    public function getAllFilteredNotificationForNotificationList($userId, $day, $childId)
    {
        if ($day == 'today') {
            $date = date('Y-m-d H:i:s', strtotime(todayZendDate()));
        } else {
            $date = null;
        }
        $notiData = $this->_tblNotification->extractAllFilteredNotification($userId, $day, $childId); //get all notification
        return $notiData; //returns array
    }

    /**
     * @desc Function to get all notifications
     * @param $userId, $childId
     * @return Array
     */
    public function getAllNotifications($userId, $childId = null, $topPopover, $limitRec, $offset)
    {

        $getNotificationData = $this->_tblNotification->getAllNotifications($userId, $childId, $topPopover, $limitRec, $offset); //get all notifications

        /* format the notifications text and return the array */

        $i = 0;
        $notificationArray = array();
        if (!empty($getNotificationData)) {

            /* get unread notification for user: for header popover notification */
            if (isset($getNotificationData[0]['unreadnotifications']) && $getNotificationData[0]['unreadnotifications'] != '') {
                $notificationArray[$i]['unreadnotifications'] = $getNotificationData[0]['unreadnotifications'];
            }

            foreach ($getNotificationData as $gData) {

                $notificationArray[$i]['notification_id'] = $gData['notification_id'];
                $notificationArray[$i]['device_id'] = $gData['device_id'];
                $notificationArray[$i]['seen_by_user'] = $gData['seen_by_user'];

                /* format the created date */

                $changedDate = date('M j, Y', strtotime($gData['created_date']));

                if (date('Y-m-d', strtotime($gData['created_date'])) == date('Y-m-d', strtotime(date("Y-m-d H:i:s")))) {
                    $changedDate = 'Today';
                } elseif (date('Y-m-d', strtotime($gData['created_date'])) == date('Y-m-d', strtotime(date("Y-m-d H:i:s") . "-1 day"))) {
                    $changedDate = 'Yesterday';
                }

                $time = date('H:i:s', strtotime($gData['created_date']));

                $notificationArray[$i]['created_date'] = $changedDate;
                $notificationArray[$i]['created_time'] = $time;

                $notificationArray[$i]['description'] = $gData['description'];
                $notificationArray[$i]['notification_type'] = $gData['notification_type'];
                $notificationArray[$i]['child_id'] = $gData['child_id'];
                $notificationArray[$i]['gender'] = $gData['gender'];

                if ($gData['notification_type'] == 'LOCK') {

                    //getting notification status
                    $notificationstatus = $this->_tblNotificationStatus->getNotificationStatus($gData['notification_id']);
                    if ($notificationstatus == true) {

                        $notificationArray[$i]['processed'] = 'false';
                    } else {

                        $notificationArray[$i]['processed'] = 'true';
                    }
                } else {

                    $notificationArray[$i]['processed'] = 'false';
                }

                $notificationArray[$i]['name'] = $gData['firstname'] . " " . $gData['lastname'];
                if (strlen(html_entity_decode($notificationArray[$i]['name'])) > 15) {
                    $notificationArray[$i]['name'] = htmlentities(substr(html_entity_decode($notificationArray[$i]['name']), 0, 15)) . '...';
                }
                $notificationArray[$i]['image'] = $gData['image'];
                $i++;
            }
        }

        return $notificationArray;
    }

    /**
     * @desc Function to get notification for device
     * @param $deviceId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getNotificationForDevieScorecard($deviceId, $childId)
    {
        $deviceNoti = $this->_tblNotification->GetNotificationForDevice($deviceId, $childId);
        if (!empty($deviceNoti['notification_id'])) {
            $notiId = $deviceNoti['notification_id'];
            $status = $this->_tblNotificationStatus->getNotificationStatus($notiId);
            if (!$status) {
                $resultStatus = array('notification_type' => 'LOCK',
                    'notification_id' => $notiId);
            } else {
                $resultStatus = array('notification_type' => 'Unlocked',
                    'notification_id' => $notiId);
            }
        }
        return $resultStatus; //returns array
    }

}
