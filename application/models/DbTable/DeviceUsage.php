<?php

/*
 * This is a model class for Device App Information
 * Created By Suman Khatri
 * Thursday, November 1, 2013 
 */

class Application_Model_DbTable_DeviceUsage extends Zend_Db_Table_Abstract
{

    // This is the name of Table
    protected $_name = 'bal_phone_spent_time_log';

    /*
     * this function is used to add device spent time
     * @param data and device key
     * created by suman
     * on November 1, 2013
     */

    public function AddDeviceUsageDetail($data)
    {
        return $this->insert($data);
    }

    /*
     * this function is used to update device spent time
     * @param where condition
     * created by suman
     * on November 1, 2013
     */

    public function UpdateDeviceUsageDetail($data, $where)
    {
        return $this->update($data, $where);
    }

    /*
     * this function is used to check device app existance
     * @param where condition
     * created by suman
     * on November 1, 2013
     */

    public function CheckPhoneTimeExistance($where)
    {
        $data = $this->fetchRow($where);
        return $data;
    }

    /*
     * this function is used to get device spent time for today
     * @param $deviceId 
     * created by suman
     * on December 3, 2013
     */

    public function getDeviceUsageForTheDay($deviceId, $childId)
    {
        $toDay = date('Y-m-d', strtotime(todayZendDate()));
        $where = "child_device_id = $deviceId and child_id = $childId and date = '$toDay'"; //where condition
        $data = $this->fetchRow($where); //fetches record on condition
        return $data; //return array
    }

    /*
     * this function is used to get device spent time for today
     * @param $deviceId 
     * @param $childId 
     */

    public function getDailyDeviceUsage($deviceId, $childId)
    {
        $data = $this->fetchRow(array(
            'child_device_id = ?' => $deviceId,
            'child_id = ?' => $childId,
            'date = ?' => date('Y-m-d')
        ));
        return $data; //return array
    }

    /*
     * this function is used to get device spent time for week
     * @param $deviceId
     * created by suman
     * on December 3, 2013
     */

    public function getDeviceUsageForWeek($deviceId, $firstDayOfWeek, $lastDayOfWeek, $childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('bpstl' => 'bal_phone_spent_time_log'), array("SUM(if(bpstl.child_device_id = '$deviceId',bpstl.duration,0)) as totalSpentTime"));
        $select->where("bpstl.date >= ?", $firstDayOfWeek);
        $select->where("bpstl.date <= ?", $lastDayOfWeek);
        $select->where("bpstl.child_id = ?", $childId);
        //echo $select;die;
        $resWeek = $db->fetchRow($select);
        return $resWeek; //return array
    }

    /*
     * this function is used to get device spent time for month
     * @param $deviceId
     * created by suman
     * on December 3, 2013
     */

    public function getDeviceUsageForMonth($deviceId, $childId)
    {
        //getting month start date and end date
        $startDateOfMonth = date('Y-m-1 0:0:0', strtotime(todayZendDate()));
        $endDateOfMonth = todayZendDate();
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array(
                    'bpstl' => 'bal_phone_spent_time_log'
                        ), array(
                    "SUM(if(bpstl.child_device_id = '$deviceId',bpstl.duration,0)) as totalSpentTime"
                ))->where("bpstl.date >= '$startDateOfMonth' AND bpstl.date <='$endDateOfMonth' and bpstl.child_id = $childId");
        $resMonth = $db->fetchRow($select);
        return $resMonth; //return array
    }

    /*
     * this function is used to get device spent time for month
     * @param $deviceId
     * created by suman
     * on December 5, 2013
     */

    public function getTotalDeviceUsage($deviceId, $childId)
    {
        //getting month start date and end date
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array(
                    'bpstl' => 'bal_phone_spent_time_log'
                        ), array(
                    "SUM(if(bpstl.child_device_id = '$deviceId',bpstl.duration,0)) as totalSpentTime"
                ))
                ->where("bpstl.child_id = $childId");
        $resTotal = $db->fetchRow($select);
        return $resTotal; //return array
    }

}
