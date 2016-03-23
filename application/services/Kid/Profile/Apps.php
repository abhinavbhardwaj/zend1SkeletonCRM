<?php

/**
 * PHP version 5
 * 
 * @category  Service_Kid
 * @package   Kid
 * @author    Ashwini Asgarwal <ashwini.agarwal@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_Kid_Profile_Apps
{

    /**
     * construct funtion
     */
    public function __construct()
    {
        
    }

    public function changeAppType($data)
    {
        $dbApps = new Application_Model_Apps();
        $appData = json_decode($data['apps']);

        foreach ($appData as $row) {
            $arrD = (array) $row;
            if ($arrD['is_prod'] == 'N') {
                $dbApps->updateAppToProductive($arrD['appId']);
            } elseif ($arrD['is_prod'] == 'Y') {
                $dbApps->updateAppToUnProductive($arrD['appId']);
            }
        }

        return array(
            'message' => "successful",
            'status_code' => '110011'
        );
    }

    public function getapps($childId, $deviceId)
    {
        $tblDeviceApps = new Application_Model_DbTable_DeviceApps();
        $data = $tblDeviceApps->GetAllApps($childId, $deviceId);
        $appData = array();
        foreach ($data as $d) {
            $arr['app_id'] = $d['app_id'];
            $arr['app_name'] = stripslashes($d['app_name']);
            $arr['package_name'] = $d['package_name'];
            $arr['unproductive'] = $d['unproductive'];
            $arr['childId'] = $d['child_id'];
            array_push($appData, $arr);
        }

        if (!empty($appData)) {
            return $appData;
        }

        return array(
            'status_code' => STATUS_ERROR,
            'message' => 'No unproductive app found'
        );
    }

}
