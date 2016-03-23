<?php

/**
 * 
 */
class Application_Service_Trophy {

    public function __construct() {
        
    }

    public function get($data) {
        $grade = $data['grade'];
        $dbTrophy = new Application_Model_Trophy();
        $data = $dbTrophy->getTrophyData($grade)->toArray();

        if (!empty($data)) {
            foreach ($data as &$value) {
                if (!empty($value['image'])) {
                    $value['image'] = AWS_S3_URL . 'trophy/' . $value['image'];
                }
            }
            $newArray['status_code'] = STATUS_SUCCESS;
            $newArray['trophy_list'] = $data;
            $newArray['message'] = "successfull";
        } else {
            $newArray['status_code'] = STATUS_ERROR;
            $newArray['trophy_list'] = array();
            $newArray['message'] = "No trophy exist in system";
        }
        return $newArray;
    }

}
