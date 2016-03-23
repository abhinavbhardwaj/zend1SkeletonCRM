<?php

/**
 * 
 */
class Application_Service_Kid_TrophyData
{

    public function __construct()
    {
        include_once APPLICATION_PATH . '/../library/functions.php';
    }

    public function get($data)
    {
        $objAuth = new Application_Service_User_AuthDevice();
        $validateAuth = $objAuth->authenticate($data['device_key'], $data['access_token'], $data['childId']);
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            return Zend_Json::encode($validateAuth);
        }

        $childId = $data['childId'];

        $dbChildTrophy = new Application_Model_DbTable_ChildTrophy();
        $trophyData = $dbChildTrophy->fetchAll(array('child_id = ?' => $childId))->toArray();

        $newArray = array();
        if (!empty($trophyData)) {
            foreach ($trophyData as &$value) {
                if (!empty($value['image'])) {
                    $value['image'] = AWS_S3_URL . 'trophy/' . $value['image'];
                }
            }
            $newArray['status_code'] = STATUS_SUCCESS;
            $newArray['trophy_list'] = $trophyData;
            $newArray['message'] = "successfull";
        } else {
            $newArray['status_code'] = STATUS_ERROR;
            $newArray['trophy_list'] = array();
            $newArray['message'] = "No trophy awarded to child";
        }
        return $newArray;
    }

}
