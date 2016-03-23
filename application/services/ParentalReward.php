<?php

/**
 * Parental Reward Services
 */
class Application_Service_ParentalReward {

    protected $_db;

    public function __construct() {
        $this->_db = new Application_Model_ParentalReward();
    }

    public function save($data, $type = 'web') {

        if (($response = $this->validateData($data))) {
            return $this->formatResponse($response, $type);
        }

        try {
            $dbChild = new Application_Model_Child();

            $insert = array(
                'child_id' => $data['child_id'],
                'child_points' => $dbChild->getChildsTotalPoints($data['child_id']),
                'reward_points' => $data['reward_points'],
                'reward_title' => $data['reward_title']
            );

            $this->_db->save($insert);
            $dbChild->sendPushToAllDevices($data['child_id'], array(
                'process_code' => 'new reward',
                'message' => 'You get a "' . $insert['reward_title'] . '" if you score the ' . $insert['child_points'] . ' + ' . $insert['reward_points'] . ' = ' . ($insert['child_points'] + $insert['reward_points']) . ' finny coins.',
                'data' => ''
            ));

            $response = array(
                'message' => 'Reward added successfully',
                'status' => 'success',
                'status_code' => '110011'
            );
        } catch (Exception $e) {
            $response = array(
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => '110013'
            );
        }

        return $this->formatResponse($response, $type);
    }

    public function getReportByChild($child_id, $where, $sOrder = NULL, $sortOr = NULL) {
        $data = $this->_db->getListByChildBy($child_id, $where, $sOrder, $sortOr);
        if (empty($data)) {
            $data = array();
        }
        return $data;
    }

    public function getListByChildId($request, $type = 'mobile') {

        if (empty($request['child_id'])) {
            $response = array(
                'message' => 'child id cannot be blank',
                'status' => 'error',
                'status_code' => '110012'
            );

            return $this->formatResponse($response, $type);
        }

        $child_id = $request['child_id'];
        $data = $this->_db->getListByChildBy($child_id, NULL, 'date_start', 'DESC');

        if ($data) {
            $return = array(
                'data' => $data,
                'status' => 'success',
                'status_code' => '110011'
            );
        } else {
            $return = array(
                'message' => 'No reward awarded yet',
                'status' => 'error',
                'status_code' => '110012'
            );
        }

        return $this->formatResponse($return, $type);
    }

    public function awardTheReward($childId = NULL) {
        $dbChild = new Application_Model_Child();
        $data = $this->_db->getInProgressRewards($childId);

        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblParentInfo = new Application_Model_DbTable_ParentInfo();

        if (empty($data)) {
            return true;
        }

        /* @var $row Zend_Db_Table_Row */
        foreach ($data as $row) {
            if ($dbChild->getChildsTotalPoints($row->child_id) >= ($row->reward_points + $row->child_points)) {

                $this->_db->update(array(
                    'is_achieved' => 1,
                    'date_end' => date('Y-m-d H:i:s')
                        ), $row->id);

                $childInfo = $dbChild->getChildBasicInfo($row->child_id);
                $insertNotifdata = array(
                    'user_id' => $tblParentInfo->isExistsParentDataWithParId($childInfo['parent_id'], 'arrayRow')->user_id,
                    'notification_type' => 'REWARD',
                    'description' => "achieved " . htmlentities($row->reward_title) . " after attaining the " . number_format($row->reward_points) . " points.",
                    'seen_by_user' => 'N',
                    'deleted' => 'N',
                    'child_device_id' => '0',
                    'childe_name' => $childInfo['name'],
                    'child_id' => $row->child_id,
                    'created_date' => date('Y-m-d H:i:s')
                );
                $tblParentNofic->AddParentNotification($insertNotifdata);

                $dbChild->sendPushToAllDevices($row->child_id, array(
                    'process_code' => 'reward awarded',
                    'message' => 'You have rewarded with "' . $row->reward_title . '"',
                    'data' => ''
                ));
            }
        }

        return TRUE;
    }

    public function getLatestRewardByChildId($child_id) {
        return $this->_db->getLatestRewardByChildId($child_id);
    }

    public function validateData($data) {
        $message = '';
        if (empty($message) && empty($data['child_id'])) {
            $message = 'child id cannot be blank';
        }

        if (empty($message) && empty($data['reward_title'])) {
            $message = 'Please enter reward title';
        }

        if (empty($message) && empty($data['reward_points'])) {
            $message = 'Please enter reward points';
        }

        if (empty($message) && !is_numeric($data['reward_points'])) {
            $message = 'Please enter valid reward points';
        }

        if (!empty($message)) {
            $messageArray = array(
                'message' => $message,
                'status' => 'error',
                'status_code' => '110012'
            );

            return $messageArray;
        }
        return FALSE;
    }

    public function awardTheRewardToKid($child_id) {
        $filePath = APPLICATION_PATH . '/../scripts/reward.php';
        $app_env = APPLICATION_ENV;
        exec("php $filePath --child_id=$child_id --APPLICATION_ENV=$app_env > /dev/null &");
        return TRUE;
    }

    public function formatResponse($response, $type) {
        if ($type == 'web') {
            unset($response['status_code']);
        } else {
            unset($response['status']);
        }
        return $response;
    }

}
