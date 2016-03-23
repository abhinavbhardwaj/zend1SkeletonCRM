<?php

/**
 * 
 */
class Application_Service_Subject {

    public function __construct() {
        
    }

    public function get() {
        $dbSubject = new Application_Model_DbTable_Framework();
        $data = $dbSubject->getSubjectLsit();
        if (!empty($data)) {
            $newArray['status_code'] = STATUS_SUCCESS;
            $newArray['subject_list'] = $data;
            $newArray['message'] = "successfull";
        } else {
            $newArray['status_code'] = STATUS_ERROR;
            $newArray['subject_list'] = null;
            $newArray['message'] = "No subject exist in system";
        }
        return $newArray;
    }

}
