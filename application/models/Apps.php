<?php

class Application_Model_Apps extends Zend_Loader_Autoloader {

    /**
     * @desc Function to get Apps log for device 
     * @param $childDeviceid,$date
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    private $_tblAppsLog;
    private $_tblChildDeviceApp;
    private $_tblParentDeviceApp;

    /*
     * function for create all model table object used this object to call model
     *  table functions
     */

    public function __construct() {
        $this->_tblAppsLog = new Application_Model_DbTable_DeviceAppLog ();
        $this->_tblChildDeviceApp = new Application_Model_DbTable_DeviceApps ();
        $this->_tblParentDeviceApp = new Application_Model_DbTable_ParentDeviceApps ();
    }

    /**
     * @desc Function to get Apps log
     * @param $childDeviceid
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function getAppsLogForDevice($childDeviceId, $toDate, $fromDate, $sOrder = null, $sortOr = null, $sWhere = null, $childId
    ) {
        if (empty($toDate) || empty($fromDate)) {
            if (date('w') == 0) {
                $fromDate = date('Y-m-d');
            } else {
                $fromDate = date('Y-m-d', strtotime('Last Sunday', time()));
            }
            $toDate = date('Y-m-d', strtotime(date("Y-m-d H:i:s")));
        }
        $appsLog = $this->_tblAppsLog->getAppsLog($childDeviceId, $toDate, $fromDate, $sOrder, $sortOr, $sWhere, $childId);
        $resultArray = array(
            'appsLog' => $appsLog,
            'toDate' => $toDate,
            'fromDate' => $fromDate
        );
        return $resultArray; //returns array
    }

    /**
     * @desc Function to update App to productive from unproductive
     * @param $appId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function updateAppToProductive($appId) {
        //creates object for model file DeviceApps		
        //data to be updated
        $updateChildAppaData = array(
            'unproductive' => 'N'
        );
        $chnageAppUnproductive = $this->_tblChildDeviceApp->UpdateAppsData(
                $updateChildAppaData, $appId
        ); //update data
        return $chnageAppUnproductive; //returns result
    }

    /**
     * @desc Function to update App to unproductive from productive
     * @param $appId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function updateAppToUnProductive($appId) {
        //data to be updated
        $updateChildAppaData = array(
            'unproductive' => 'Y'
        );
        $chnageAppUnproductive = $this->_tblChildDeviceApp->UpdateAppsData(
                $updateChildAppaData, $appId
        ); //update data
        return $chnageAppUnproductive; //returns result
    }

    /**
     * @desc Function to update App type
     * @param $appdate $appId
     * @param $appId
     * @return result
     */
    public function updateAppType($appData, $appId) {
        return $this->_tblChildDeviceApp->UpdateAppsProdUnprod($appData, $appId);
    }

    /**
     * @desc Function to get All apps for device
     * @param $deviceId
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getAllAppsForDevice($deviceId, $childId) {
        //creates object for model file DeviceApps		
        $childDeviceApps = $this->_tblChildDeviceApp->getAllChildDeviceApps(
                $deviceId, $childId
        ); //fetches all apps for device
        return $childDeviceApps; //returns array
    }

    /**
     * @desc Function to get All apps for device
     * @param $deviceId
     * @param $childId
     * @return ArrayIterator
     */
    public function getAllAppsInDevice($deviceId, $childId, $excludeFinny = TRUE) {
        //fetches all apps for device
        $childDeviceApps = $this->_tblChildDeviceApp->getAllAppsInDevice($deviceId, $childId, $excludeFinny);
        return $childDeviceApps;
    }

    /**
     * @desc Function to get All unproductive apps for device
     * @param $deviceId
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getAllUnproductiveAppsForDevice($deviceId, $childId) {
        //creates object for model file DeviceApp
        //fetches all unproductive apps for device	
        $childDeviceApps = $this->_tblChildDeviceApp
                ->getAllChildUnproductiveDeviceApps($deviceId, $childId);
        return $childDeviceApps; //returns array
    }

    /**
     * @desc Function to get All unproductive apps for device
     * @param $deviceId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    /* public function getAllproductiveAppsForDevice($deviceId) {
      //fetches all unproductive apps for device
      $childDeviceApps 	= $this->_tblChildDeviceApp
      ->getAllChildproductiveDeviceApps ( $deviceId );
      return $childDeviceApps; //returns array
      } */

    /**
     * @desc Function to get DeviceId from app
     * @param $appId
     * @author --
     * @return ArrayObject
     */
    public function getdeviceIdFromAppId($appId) {
        $appListData = $this->_tblChildDeviceApp->getDiviceId($appId);
        return $appListData;
    }

    /**
     * @desc Function to delet app
     * @param $appId
     * @author Suman Khatri on 27th Dec 2013
     * @return $result
     */
    public function deleteApps($childDeviceId, $childId) {
        $where = "child_device_id = $childDeviceId and child_id = $childId";
        $result = $this->_tblChildDeviceApp->removeDeviceApps($where);
        return $result;
    }

    /**
     * @desc Function to insert app
     * @param int $deviceId // ,$childId,$appName,$packageName,$fileName,$status
     * @author Suman Khatri on 27th Dec 2013 updated in 9th October 2014 By Suman Khatri
     * @return $result
     */
    public function insertChildApp($deviceId, $childId, $appName, $packageName, $fileName, $status
    ) {
        $insertdata = array('child_device_id' => $deviceId,
            'child_id' => $childId,
            'app_name' => $appName,
            'package_name' => $packageName,
            'app_image' => $fileName,
            'unproductive' => $status,
            'created_date' => date("Y-m-d H:i:s")
        );
        $resApps = $this->_tblChildDeviceApp->AddDeviceApps($insertdata);
        return $resApps;
    }

    /**
     * @desc Function to get Apps log
     * @param $appId, $toDate, $fromDate
     * @author suman khatri on 9th jan 2014
     * @return result
     */
    public function getLogForApp($appId, $toDate, $fromDate, $deviceId, $packegaName, $childId = NULL) {
        $appLogTotal = array();
        $j = 1;
        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));
        while (strtotime($toDate) >= strtotime($fromDate)) {
            $appsLog = $this->_tblAppsLog->getLogForApp($appId, $toDate, $deviceId, $packegaName, $childId);
            if (!empty($appsLog['duration']) && $appsLog['duration'] != null) {
                $appLogTotal[$j]['date'] = date("M j, Y", strtotime($appsLog['date']));
                $appLogTotal[$j]['duration'] = My_Functions::secondsToHHMMSS($appsLog['duration']);
            } else {
                $appLogTotal[$j]['date'] = date("M j, Y", strtotime($toDate));
                $appLogTotal[$j]['duration'] = My_Functions::secondsToHHMMSS(0);
            }
            $j++;
            unset($appsLog);
            $toDate = date('Y-m-d', strtotime("-1 day", strtotime($toDate)));
        }
        return $appLogTotal; //returns array
    }

    /**
     * @desc Function to get App detail from log
     * @param $appId
     * @author suman khatri on n 9th jan 2014
     * @return ArrayObject
     */
    public function getAppDetail($appId) {
        $appsDetail = $this->_tblAppsLog->getAppName($appId);
        return $appsDetail; //returns array
    }

    /**
     * @desc Function to get over all App found in system
     * @param NILL
     * @author suman khatri on 15th april 2014
     * @return result
     */
    public function getOverAllApps() {
        $appsDetail = $this->_tblChildDeviceApp->getOverAllApps();
        if (!empty($appsDetail) && $appsDetail > 0) {
            return $appsDetail; //returns result
        } else {
            return false;
        }
    }

    /**
     * @desc Function to get log for application used by chilren
     * @param $childDeviceId, $date
     * @author suman khatri on 15th April 2014
     * @return ArrayIterator
     */
    public function getLogForAllAppsUsedByChild() {
        $result = $this->_tblAppsLog->getLogForAllAppsUsedByChild();
        $result = array_map('current', $result);
        $result = array_count_values($result);
        $formatedArray = array();
        $formatedFinalArray = array();
        $arrayD = array();
        if (!empty($result) && $result != null) {
            $formatedArray[0]['id'] = '';
            $formatedArray[0]['label'] = 'Application';
            $formatedArray[0]['pattern'] = '';
            $formatedArray[0]['type'] = 'string';
            $formatedArray[1]['id'] = '';
            $formatedArray[1]['label'] = '# of children';
            $formatedArray[1]['pattern'] = '';
            $formatedArray[1]['type'] = 'number';
            $formatedFinalArray['cols'] = $formatedArray;
            unset($formatedArray);
            $i = 0;
            foreach ($result as $key => $value) {
                $formatedArray[0]['v'] = $key;
                $formatedArray[0]['f'] = null;
                $formatedArray[1]['v'] = $value;
                $formatedArray[1]['f'] = null;
                $arrayD[$i]['c'] = $formatedArray;
                unset($formatedArray);
                $i++;
            }
            $formatedFinalArray['rows'] = $arrayD;
            return json_encode($formatedFinalArray);
        } else {
            return false;
        }
    }

    /**
     * @desc Function to delete apps for child and device
     * @param int $childId child id 
     * @param int $deviceAddId device id
     * @author suman khatri on 9th October 2014
     * @return result
     */
    public function removeDeviceAppsForChildAndDevice($childDeviceId) {
        $result = $this->_tblChildDeviceApp->removeDeviceAppsForChildAndDevice($childDeviceId);
        return $result; //return result
    }

    /**
     * @desc Function to delete apps for child and device
     * @param int $parentId parent id 
     * @param int $deviceId device id
     * 
     * @author suman khatri on 9th October 2014
     * @return result
     */
    public function removeDeviceAppsForParentAndDevice($deviceId) {
        $result = $this->_tblParentDeviceApp->removeDeviceAppsForParentAndDevice($deviceId);
        return $result; //return result
    }

    /**
     * @desc Function to insert app
     * @param int    $parentId parent id 
     * @param int    $deviceId device id
     * @param string $appName name of app
     * @param string $packageNam package name
     * @param string $fileName image name
     * @param string $status status
     * @author Suman Khatri on 21st October 2014
     * @return $result
     */
    public function insertParentApp($deviceId, $parentId, $appName, $packageName, $fileName
    ) {
        $insertdata = array('device_id' => $deviceId,
            'parent_id' => $parentId,
            'app_name' => $appName,
            'package_name' => $packageName,
            'app_image' => $fileName,
            'created_date' => date("Y-m-d H:i:s")
        );
        $resApps = $this->_tblParentDeviceApp->AddParentDeviceApps($insertdata);
        return $resApps;
    }

    public function assignParentAppsToAllKid($parentId, $deviceId = NULL) {
        $dbKid = new Application_Model_DbTable_ChildInfo();
        $kidsData = $dbKid->getChildbasicinfo($parentId);
        $dbParentDeviceApp = new Application_Model_DbTable_ParentDeviceApps();
        $apps = $dbParentDeviceApp->getParentDeviceApps($parentId, $deviceId);
        $dbKidDeviceApp = new Application_Model_DbTable_DeviceApps();
        foreach ($kidsData as $kData) {
            $kidId = $kData['child_id'];
            foreach ($apps as $app) {
                $this->insertChildApp($app->device_id, $kidId, $app->app_name, $app->package_name, $app->app_image, 'Y'
                );
                //$dbKidDeviceApp->insert($data);
            }
        }
    }

    public function updateAppsToAllChild($parentId, $deviceId, $appName, $packageName, $fileName) {
        $dbKid = new Application_Model_DbTable_ChildInfo();
        $kidsData = $dbKid->getChildbasicinfo($parentId);
        $dbParentDeviceApp = new Application_Model_DbTable_ParentDeviceApps();
        $apps = $dbParentDeviceApp->getParentDeviceApps($parentId, null);
        $dbKidDeviceApp = new Application_Model_DbTable_DeviceApps();

        $dbDefaultApp = new Application_Model_DbTable_DeviceDefaultApps();
        $status = $dbDefaultApp->isProductiveApp($packageName) ? 'N' : 'Y';

        foreach ($kidsData as $kData) {
            $kidId = $kData['child_id'];
            $this->insertChildApp($deviceId, $kidId, $appName, $packageName, $fileName, $status
            );
        }
    }

    public function removeAppsToAllChild($parentId, $deviceId, $appName, $packageName) {
        $dbKid = new Application_Model_DbTable_ChildInfo();
        $dbKidDeviceApp = new Application_Model_DbTable_DeviceApps();
        $kidsData = $dbKid->getChildbasicinfo($parentId);
        foreach ($kidsData as $kData) {
            $kidId = $kData['child_id'];
            $resparApps = $dbKidDeviceApp->delete(array(
                "child_device_id = ?" => $deviceId,
            //    "app_name = ?" => $appName,
                "package_name = ?" => $packageName,
                "child_id = ?" => $kidId
            ));
        }
    }
    
    public function getTopFiveAppsOfChild($weeklyGoalDateRange, $childId) {
        $tblDeviceAppLog = new Application_Model_DbTable_DeviceAppLogDetail();
        $appLog = $tblDeviceAppLog->getTopFiveAppUsedByChild($weeklyGoalDateRange, $childId);
        
        //$appLogData = arraymsort($appLog, array("duration" => 'SORT_DESC'));
        $appLogdata = array();
        
        $cols = array("duration" => 'SORT_DESC');
        
        $colarr = array(); //print_r($cols);die;
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($appLog as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k]))
                    $ret[$k] = $appLog[$k];
                $ret[$k][$col] = $appLog[$k][$col];
            }
        }
        
        if (!empty($ret)) {
            $i = 0;
            foreach ($ret as $appData) {
                $appLogdata[$i]['app_name'] = $appData['app_name'];
                $appLogdata[$i]['duration'] = My_Functions::secondsToHHMMSS($appData['duration'], FALSE);
                $appLogdata[$i]['app_image'] = $appData['app_image'];
                $i++;
            }
        }
        return $appLogdata;
    }

    public function getAppsLogForStats($childId, $deviceId) {
        $tblAppsLogDetail = new Application_Model_DbTable_DeviceAppLogDetail();
        $todayAppsLog = $tblAppsLogDetail->getAppsLogToday($childId, $deviceId);
        $thisWeekAppsLog = $tblAppsLogDetail->getAppsLogThisWeek($childId, $deviceId);
        $thisMonthAppsLog = $tblAppsLogDetail->getAppsLogThisMonth($childId, $deviceId);
        $appLogDataToday = arraymsort($todayAppsLog, array("duration" => 'SORT_DESC'));
        
        if (!empty($appLogDataToday)) {
            $i = 0;
            foreach ($appLogDataToday as $appDataToday) {
                $appLogdataToday[$i]['app_name'] = $appDataToday['app_name'];
                $appLogdataToday[$i]['package_name'] = $appDataToday['package_name'];
                $appLogdataToday[$i]['duration'] = $appDataToday['duration'];
                $appLogdataToday[$i]['app_image'] = AWS_S3_URL . 'apps/' . $appDataToday['app_image'];
                $i++;
            }
        } else {
            $appLogdataToday = array();
        }
        $appLogDataThisWeek = arraymsort($thisWeekAppsLog, array("duration" => 'SORT_DESC'));
        if (!empty($appLogDataThisWeek)) {
            $i = 0;
            foreach ($appLogDataThisWeek as $appDataWeek) {
                $appLogdataThisWeek[$i]['app_name'] = $appDataWeek['app_name'];
                $appLogdataThisWeek[$i]['package_name'] = $appDataWeek['package_name'];
                $appLogdataThisWeek[$i]['duration'] = $appDataWeek['duration'];
                $appLogdataThisWeek[$i]['app_image'] = AWS_S3_URL . 'apps/' . $appDataWeek['app_image'];
                $i++;
            }
        } else {
            $appLogdataThisWeek = array();
        }
        $appLogDataMonth = arraymsort($thisMonthAppsLog, array("duration" => 'SORT_DESC'));
        if (!empty($appLogDataMonth)) {
            $i = 0;
            foreach ($appLogDataMonth as $appDataMonth) {
                $appLogdataThisMonth[$i]['app_name'] = $appDataMonth['app_name'];
                $appLogdataThisMonth[$i]['package_name'] = $appDataMonth['package_name'];
                $appLogdataThisMonth[$i]['duration'] = $appDataMonth['duration'];
                $appLogdataThisMonth[$i]['app_image'] = AWS_S3_URL . 'apps/' . $appDataMonth['app_image'];
                $i++;
            }
        } else {
            $appLogdataThisMonth = array();
        }
        return array(
            'todayAppLog' => $appLogdataToday,
            'weekAppLog' => $appLogdataThisWeek,
            'monthAppLog' => $appLogdataThisMonth
        );
    }

    /**
     * Check if app already associated with parent
     * 
     * @param $parentId
     * @param $deviceId
     * @param $package_name
     * @return boolean
     */
    public function isAlreadyExists($parentId, $deviceId, $package_name)
    {
        $appExists = $this->_tblParentDeviceApp->fetchRow(array(
            "parent_id = ?" => $parentId,
            "device_id = ?" => $deviceId,
            "package_name = ?" => $package_name
        ));

        return boolval($appExists);
    }
}
