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
class Application_Service_Kid_Profile_DefaultLearningCustomisation {

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectchild;

    /**
     * construct funtion
     */
    public function __construct() {
        // creates object of class child
        $this->_objectchild = new Application_Model_Child();
    }

    public function get($data = array()) {
        try {
            $lockDeviceTime = $this->_objectchild->getAllLockDeviceTime();
            $questionNum = $this->_objectchild->getAllQuestionNumData();
            $askTime = $this->_objectchild->getAllAskTimeData();

            if (!empty($data['child_id'])) {
                $childInfoArray = $this->_objectchild->getChildInfoArray($data['child_id']);
                $childInfo = $childInfoArray[0];

                if (!in_array($childInfo['unlock_time'], $lockDeviceTime)) {
                    $lockDeviceTime[] = $childInfo['unlock_time'];
                }
                if (!in_array($childInfo['no_of_questions'], $questionNum)) {
                    $questionNum[] = $childInfo['no_of_questions'];
                }
                if (!in_array($childInfo['time'], $askTime)) {
                    $askTime[] = $childInfo['time'];
                }
            }

            asort($lockDeviceTime);
            asort($questionNum);
            asort($askTime);

            $return = array(
                'lock_device_time' => array_values($lockDeviceTime),
                'question_count' => array_values($questionNum),
                'trigger_interwell' => array_values($askTime)
            );

            // block to add or update custome message end
            $messageArray = array(
                'data' => $return,
                'message' => 'success',
                'status' => 'success',
                'status_code' => '110011'
            );
        } catch (Exception $e) {
            $messageArray = array(
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => '110013',
                'data' => NULL
            );
        }

        return $this->formatResponse($messageArray);
    }

    public function formatResponse($response, $type = 'mobile') {

        if ($type == 'web') {
            unset($response['status_code']);
        } else {
            unset($response['status']);
        }

        return $response;
    }

}
