<?php

/**
 * 
 */
class Application_Service_Kid_Streak {

    public function __construct() {
        include_once APPLICATION_PATH . '/../library/functions.php';
    }

    public function award($data) {
        $objAuth = new Application_Service_User_AuthDevice();
        $objParent = new Application_Model_Parents();
        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $validateAuth = $objAuth->authenticate($data['device_key'], $data['access_token'], $data['childId']);
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            return Zend_Json::encode($validateAuth);
        }
        $parId = $validateAuth['parentId'];
        $deviceId = $validateAuth['deviceId'];
        $childId = $data['childId'];
        $streaks = json_decode($data['streaks'], TRUE);
        $pushData = array();
        $childInfo = $tblChildInfo->fetchRow("child_id = $childId");
        $childName = $childInfo['name'];
        $parentInfo = $tblParentInfo->fetchRow("parent_id = $parId");
        $userId = $parentInfo['user_id'];
        
        try {
            $tblchildTrophy = new Application_Model_DbTable_ChildTrophy();
            foreach ($streaks as $streak) {
                $subjectId = $streak['subject_id'] ? : NULL;
                
                $image = str_replace(AWS_S3_URL . 'trophy/', "", $streak['image']);
                
                if (!$tblchildTrophy->getExisttrophy($childId, $streak['title'], $streak['description'], $image, $subjectId, $streak['grade_id'])) {
                    
                    $streak_data = array(
                        'trophy_id' => $streak['trophy_id'],
                        'child_id' => $childId,
                        'subject_id' => $subjectId,
                        'grade_id' => $streak['grade_id'],
                        'title' => $streak['title'],
                        'description' => $streak['description'],
                        'image' => $image,
                        'points' => NULL,
                        'streak' => $streak['streak'],
                        'type' => $streak['type'],
                        'counter' => 1,
                        'awarded_date' => todayZendDate()
                    );
                    $streak_data['id'] = $tblchildTrophy->AddTrophy($streak_data);
                    $pushData[] = $streak_data;
                    $resnotifis = $objParent->addParentNotificationForTrophy(
                        $subjectId, null, $streak['streak'], $userId, $streak['title'], 
                        $deviceId, $childName, $childId
                    );
                }else{
                    $trophyData = $tblchildTrophy->getExisttrophy($childId, $streak['title'], $streak['description'], $image, $subjectId, $streak['grade_id']);
                    $counter = $trophyData['counter'];
                    $counter = $counter + 1;
                    $dataUpdate = array(
                        'trophy_id' => $trophyData['trophy_id'],
                        'child_id' => $trophyData['child_id'],
                        'subject_id' => $trophyData['subject_id'],
                        'grade_id' => $trophyData['grade_id'],
                        'title' => $trophyData['title'],
                        'description' => $trophyData['description'],
                        'image' => $trophyData['image'],
                        'points' => NULL,
                        'streak' => $trophyData['streak'],
                        'type' => $trophyData['type'],
                        'counter' => $counter,
                        'awarded_date' => todayZendDate()
                    );
                    $childtrophyId = $trophyData['child_trophy_id'];
                    $upadteData = $tblchildTrophy->updateData($childtrophyId,$dataUpdate);
                    $pushData[] = $dataUpdate;
                    $resnotifis = $objParent->addParentNotificationForTrophy(
                        $trophyData['subject_id'], null, $trophyData['streak'], $userId, $trophyData['title'], 
                        $deviceId, $childName, $trophyData['child_id']
                    );
                }
            }

            $objParent = new Application_Model_Parents();
            $objTrophy = new Application_Model_Trophy();
            $trophyarray = $objTrophy->sendPushOfTrophy($pushData, null, null, 'child trophy',$parId);
            $response = array(
                'message' => 'success',
                'status_code' => STATUS_SUCCESS
            );
        } catch (Exception $e) {
            $response = array(
                'message' => $e->getMessage(),
                'status_code' => STATUS_SYSTEM_ERROR
            );
        }
        return $response;
    }

}
