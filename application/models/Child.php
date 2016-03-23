<?php

class Application_Model_Child extends Zend_Loader_Autoloader {
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblChildInfo;
    private $_tblSubjectInfo;
    private $_tblCustomMessage;
    private $_tblChildGradePoint;
    private $_tblChildWeeklyGoalStat;
    private $_tblDeviceInfo;
    private $_tblGoals;
    private $_tblChildReqQues;
    private $_tblWeekGoals;
    private $_tblLockDevicefor;
    private $_tblQuestionNumAsktime;
    private $_defaultLC;
    private $_objectCategory;
    private $_objectDevice;
    private $_tblDeviceAppsLog;
    private $_tblDeviceAppsDetails;
    private $_tblDeviceApps;
    private $_tblChildQuestion;
    private $_tblChildQuestionSequence;
    private $_tblChildQuestionSequenceTrack;
    private $_tblChildRedeem;
    private $_tblChildSubject;
    private $_tblChildTrophy;
    private $_objectParent;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        // creates object of model file ChildInfo
        $this->_tblChildInfo = new Application_Model_DbTable_ChildInfo ();
        // creates object of model file ChildSubject
        $this->_tblSubjectInfo = new Application_Model_DbTable_ChildSubject ();
        // creates object of model file CustomMessage
        $this->_tblCustomMessage = new Application_Model_DbTable_CustomMessage ();
        // creates object of model file ChildGradePoints
        $this->_tblChildGradePoint = new Application_Model_DbTable_ChildGradePoints ();
        // creates object of model file ChildWeeklyGoal
        $this->_tblChildWeeklyGoalStat = new Application_Model_DbTable_ChildWeeklyGoal ();
        // creates object of model file ChildDeviceInfo
        $this->_tblDeviceInfo = new Application_Model_DbTable_ChildDeviceInfo ();
        // creates object of model file ChildGoals
        $this->_tblGoals = new Application_Model_DbTable_ChildGoals ();
        // creates object of model file ChildQuestionRequest
        $this->_tblChildReqQues = new Application_Model_DbTable_ChildQuestionRequest ();
        // creates object of model file ChildWeeklyGoal
        $this->_tblWeekGoals = new Application_Model_DbTable_ChildWeeklyGoal ();
        // creates object of model file LockDeviceFor
        $this->_tblLockDevicefor = new Application_Model_DbTable_LcLockDuration();
        // creates object of model file QuestionNumAskTime
        $this->_tblQuestionNumAsktime = new Application_Model_DbTable_LcQuestionCount();
        //creates object of class default learning customization
        $this->_defaultLC = new Application_Model_DefaultLearningCustomization ();
        // creates object of class category
        $this->_objectCategory = new Application_Model_Category ();
        // creates object of class device
        $this->_objectDevice = new Application_Model_Device ();
        // creates object of model file DeviceAppLog
        $this->_tblDeviceAppsLog = new Application_Model_DbTable_DeviceAppLog();
        // creates object of model file DeviceAppLogDetail
        $this->_tblDeviceAppsDetails = new Application_Model_DbTable_DeviceAppLogDetail();
        // creates object of model file DeviceApps
        $this->_tblDeviceApps = new Application_Model_DbTable_DeviceApps();
        // creates object of model file RequestQuestion
        $this->_tblChildQuestion = new Application_Model_DbTable_RequestQuestion();
        // creates object of model file ChildQuestionSequence
        $this->_tblChildQuestionSequence = new Application_Model_DbTable_ChildQuestionSequence();
        // creates object of model file ChildQuestionSequenceTrack
        $this->_tblChildQuestionSequenceTrack = new Application_Model_DbTable_ChildQuestionSequenceTrack();
        // creates object of model file ChildRedeemPoints
        $this->_tblChildRedeem = new Application_Model_DbTable_ChildRedeemPoints();
        // creates object of model file ChildSubject
        $this->_tblChildSubject = new Application_Model_DbTable_ChildSubject();
        // creates object of model file ChildTrophy
        $this->_tblChildTrophy = new Application_Model_DbTable_ChildTrophy();
        // creates object of class parents
        $this->_objectParent = new Application_Model_Parents ();
    }

    /*     * ********
     * function for delete all anynomous parent child data
     * * ******** */

    public function clearAllChildData($childId, $childDeviceId) {
        /*         * *************truncate table block for Apps Log Details****** */
        $dataResult = $this->_tblDeviceApps->GetAllApps($childId, $childDeviceId);
        foreach ($dataResult as $dataResultA) {
            $appId = $dataResultA['app_id'];
            $this->_tblDeviceAppsDetails->deleteData($appId);
        }
        /*         * *************End truncate table block for Apps Log Details****** */
        /*         * *************truncate table block for Weekly Goals Stats****** */
        $where = "child_id = $childId";
        $dataResult = $this->_tblWeekGoals->getWeeklyData($where);
        foreach ($dataResult as $dataResultA) {
            $weeklyGoalsId = $dataResultA['weekly_goals_id'];
            $this->_tblChildWeeklyGoalStat->deleteData($weeklyGoalsId);
        }
        /*         * *************End truncate table block for Weekly Goals Stats****** */

        /*         * *************truncate table block for Device Apps****** */
        $where = "child_device_id = $childDeviceId and child_id = $childId";
        $this->_tblDeviceApps->removeDeviceApps($where);
        /*         * *************End truncate table block for Device Apps****** */
        /*         * *************truncate table block for Device Apps log****** */
        $this->_tblDeviceAppsLog->deleteData($childDeviceId);
        /*         * *************End truncate table block for Device Apps log****** */

        /*         * *************truncate table block for child question****** */
        $this->_tblChildQuestion->deleteData($childDeviceId);
        /*         * *************End truncate table block for child question****** */
        /*         * *************truncate table block for Device Info****** */
        $this->_tblDeviceInfo->deleteData($childId);
        /*         * *************End truncate table block for Device Info****** */
        /*         * *************truncate table block for child Goals****** */
        $this->_tblGoals->deleteData($childId);
        /*         * *************End truncate table block for child Goals****** */
        /*         * *************truncate table block for child grade points****** */
        $this->_tblChildGradePoint->deleteData($childId);
        /*         * *************End truncate table block for child grade points****** */
        /*         * *************truncate table block for child question delete request****** */
        $this->_tblChildReqQues->deleteData($childId);
        /*         * *************End truncate table block for child question delete request****** */
        /*         * *************truncate table block for child question sequence track****** */
        $this->_tblChildQuestionSequenceTrack->deleteData($childId);
        /*         * *************End truncate table block for child question sequence track****** */

        /*         * *************truncate table block for child redeem****** */
        $this->_tblChildRedeem->deleteData($childId);
        /*         * *************End truncate table block for child redeem****** */

        /*         * *************truncate table block for childs subject****** */
        $this->_tblChildSubject->removeChildSubjectOnChildId($childId);
        /*         * *************End truncate table block for childs subject****** */
        /*         * *************truncate table block for child trophy****** */
        $this->_tblChildTrophy->deleteData($childId);
        /*         * *************End truncate table block for child trophy****** */
        /*         * *************truncate table block for week Goals****** */
        $this->_tblWeekGoals->deleteData($childId);
        /*         * *************End truncate table block for week Goals****** */
        /*         * *************truncate table block for child info****** */
        $this->_tblChildInfo->deleteData($childId);
        /*         * *************End truncate table block for child info****** */
    }

    /**
     * Function to get child basic info
     * 
     * @param childId
     * @author suman khatri on 13th November 2013
     * @return ArrayObject
     */
    public function getChildBasicInfo($childId) {
        $childInfo = $this->_tblChildInfo->getChildBasicsinfo($childId); // fetches childs basic info
        return $childInfo; // returns childinfo
    }

    /**
     * Function to know child is exist with parent or not
     * 
     * @param
     *        	childId,parentId
     * @author suman khatri on 13th November 2013
     * @return chekcChildExist
     */
    public function childExistWithParent($parId, $childId) {
        // checks childs exist with parent or not
        $chekcChildExist = $this->_tblChildInfo->childExistWithParChildId($parId, $childId);
        return $chekcChildExist; // return chekcChildExist;
    }

    /**
     * Function to get child's custom message
     * 
     * @param
     *        	childId
     * @author suman khatri on 13th November 2013
     * @return ArrayObject
     */
    public function getChildsCustomMessage($childId) {
        $messageData = $this->_tblCustomMessage->getCustomMesage($childId); // fetches custom message for child
        return $messageData; // returns messagedata
    }

    /**
     * Function to insert or update child's custom message
     * 
     * @param
     *        	$customMessage,$childId,$userId,$createdDate,$action
     * @author suman khatri on 13th November 2013
     * @return result
     */
    public function addOrUpdateChildsCustomMessage($customMessage, $childId, $userId, $createdDate, $action, $customMessageId) {
        // message data to be update or insert
        $messageDataAarray = array(
            'message' => $customMessage,
            'child_id' => $childId,
            'parent_id' => $userId
        );
        if ($action == 'add') { // if action is add
            $messageDataAarray ['created_date'] = $createdDate;
            $res = $this->_tblCustomMessage->insertCustomMesage($messageDataAarray); // insert data into DB
        } else { // if action is update
            $messageDataAarray ['modified_date'] = $createdDate;
            $res = $this->_tblCustomMessage->updateCustomMessage($messageDataAarray, $customMessageId); // updates data
        }
        return $res; // returns result
    }

    /**
     * Function to get child's grade info
     * 
     * @param
     *        	$childId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getGradeOfChild($childId) {
        $gradeInfo = $this->_tblChildGradePoint->GetGradeofChild($childId); // fetches all grades of child
        return $gradeInfo; // returns array
    }

    /**
     * Function to get child's weekly goal info
     * 
     * @param
     *        	$childId,$statusselect
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getWeeklyGoalOfchild($childId, $statusselect, $sOrder = null, $sortOr = null, $sWhere = null, $shaving = null) {
        // fetches all weekly goals of child
        $childWeeklyGoal = $this->_tblChildWeeklyGoalStat->getAllWeeklyData($childId, $statusselect, $sOrder, $sortOr, $sWhere, $shaving);
        return $childWeeklyGoal; // return array
    }

    /**
     * Function to get child's info using deviceId
     * 
     * @param
     *        	$deviceId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getChildIfoUsingDevieId($deviceId) {
        // checks childs exist with parent or not
        $childInfo = $this->_tblDeviceInfo->getChildInfoUsingDeviceId($deviceId);
        return $childInfo; // return array;
    }

    /**
     * Function to get child's subject
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayIterator
     */
    public function getChildSubject($childId) {
        // getting child subjectlist
        $subjectData = $this->_tblSubjectInfo->getAllChildSubject($childId);
        return $subjectData; // return array;
    }

    /**
     * Function to get current grade of child
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getCurrentGradeOfChild($childId) {
        // getting grade of child
        $checkGarade = $this->_tblChildInfo->getGradeId($childId);
        return $checkGarade; // return array;
    }

    /**
     * Function to insert grade value into gradepoint table
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function insertGradePoint($childId, $gradeId, $childPoints) {
        // data to be insert
        $data = array(
            'child_id' => $childId,
            'grade_id' => $gradeId
        );
        if (!empty($childPoints) && $childPoints != null) {
            $data ['points'] = $childPoints;
        } else {
            $data ['points'] = 0;
        }
        // insert data into DB
        $addchildpoint = $this->_tblChildGradePoint->insert($data);
        return $addchildpoint; // return result;
    }

    /**
     * *************function for update grade points*************
     */
    public function updateGradePoint($childId, $gradeId, $childPoints) {
        $where = "child_id = '$childId'";
        // data to be insert
        $data = array(
            'grade_id' => $gradeId
        );
        if (!empty($childPoints) && $childPoints != null) {
            $data ['points'] = $childPoints;
        } else {
            $data ['points'] = 0;
        }
        // insert data into DB
        $updateGradePoints = $this->_tblChildGradePoint->update($data, $where);
        return $updateGradePoints; // return result;
    }

    /**
     * Function to add or update child's basic info
     * 
     * @param
     *        	$childId,$childBasicInfo
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function addOrUpdateChildBasicInfo($childId, $childBasicInfo) {
        if (!empty($childId) && $childId != null) {
            // updates data into DB
            $result = $this->_tblChildInfo->updateChildInfo($childBasicInfo, $childId);
        } else {
            // insert data into DB
            $result = $this->_tblChildInfo->addChild($childBasicInfo);
        }
        return $result; // return result;
    }

    /**
     * Function to remove child's old subject
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    function removeChildSubject($childId) {
        // remove child old subject
        $result = $this->_tblSubjectInfo->removeChildSubjectOnChildId($childId);
        return $result; // return result;
    }

    /**
     * Function to insert child's subject
     * 
     * @param
     *        	$childId,$subjectId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function insertChildSubject($childId, $subjectId, $domainId) {
        // data to be insert
        if (!empty($subjectId)) {
            $childSubjectInfo = array(
                'subject_id' => $subjectId,
                'child_id' => $childId,
                'domain_id' => $domainId
            );
            // getting child subjectlist
            $result = $this->_tblSubjectInfo->addChildSubjectInfo($childSubjectInfo);
            return $result; // return result;
        }
    }

    /**
     * Function to add child's goal
     * 
     * @param
     *        	$childId,$askQuesTime,$chancesNo,$weeklyGoal,$unlockTime,$action
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function addOrUpdateChildGoal($childId, $askQuesTime, $chancesNo, $weeklyGoal, $unlockTime, $action, $learningType, $isInterruption) {
        // data to be insert
        $childGoalsData = array();
        $childGoalsData = array(
            'question_popup_time' => $askQuesTime,
            'number_of_questions' => $chancesNo,
            'weekly_points' => $weeklyGoal,
            'unlock_time' => $unlockTime,
            'learning_type' => $learningType
        );
        if ($action == 'add') { // inserts record into DB
            $childGoalsData ['child_id'] = $childId;
            $childGoalsData ['created_date'] = todayZendDate();
            $result = $this->_tblGoals->addChildGoals($childGoalsData);
        }
        if ($action == 'update') { // updates record into DB
            $childGoalsData ['modified_date'] = todayZendDate();
            $childGoalsData['is_interrupt'] = $isInterruption;
            $childGoalsData ['learnig_updated'] = 1;
            $result = $this->_tblGoals->updateChildGoals($childGoalsData, $childId);
        }
        return $result; // return result;
    }

    /**
     * Function to add child's weekly goal
     * 
     * @param
     *        	$childId,$weekOfGoal,$createdDate,$endDateWeekGoal,$unlockTime,$action
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function addOrUpdateChildWeeklyGoal($childId, $weekOfGoal, $createdDate, $endDateWeekGoal, $action, $weeklyGoal) {
        // data to be insert
        $childWeekGoalsData = array(
            'goal_points' => $weekOfGoal,
            'start_date' => $createdDate,
            'end_date' => $endDateWeekGoal,
            'created_date' => $createdDate,
            'closed' => 'N',
            'weekly_goal_points' => $weeklyGoal
        );
        if ($action == 'add') { // inserts record into DB
            $childWeekGoalsData ['child_id'] = $childId;
            $result = $this->_tblWeekGoals->addChildWeekGoals($childWeekGoalsData);
        } else if ($action == 'update') { // inserts record into DB
            $where = "child_id = $childId";
            $result = $this->_tblWeekGoals->update($childWeekGoalsData, $where);
        }
        return $result; // return result;
    }

    /**
     * Function to check existance of child's goal
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function isExistChildGoal($childId) {
        // checking child goal existance
        $checkChildGoalsExist = $this->_tblGoals->isExistsGoals($childId);
        return $checkChildGoalsExist; // return result;
    }

    /**
     * Function to check challenge is send to child or not
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function isSendChallengetoChild($childId) {
        // checking child goal existance
        $childChallenge = $this->_tblChildReqQues->getChildChallengesSet($childId);
        return $childChallenge; // return array;
    }

    /**
     * Function to fetch all child of a parent
     * 
     * @param
     *        	$parId
     * @author suman khatri on 27th November 2013
     * @return ArrayIterator
     */
    public function getAllChildOfParent($parId, $selectFields = array(), $sortBy = 'name', $sortOrder = 'ASC') {
        $childs = $this->_tblChildInfo->getChildbasicinfo($parId, $selectFields, $sortBy, $sortOrder); // getting all child info of parent
        return $childs; // return array
    }
    
    /**
     * Function to fetch first child with name asc
     * @param $parId
     * @return ArrayIterator
     */
    public function getSingleChildByNameAsc($parId, $selectFields = array()) {
        $childs = $this->_tblChildInfo->getSingleChildByNameAsc($parId, $selectFields); // getting all child info of parent
        return $childs; // return array
    }
    
    /**
     * Function to fetch first child with name asc
     * @param $parId
     * @return ArrayIterator
     */
    public function getSingleChildDetailsByNameAsc($parId, $selectFields = array()) {
        $childs = $this->_tblChildInfo->getSingleChildDetailsByNameAsc($parId, $selectFields); // getting all child info of parent
        return $childs; // return array
    }
    

    /**
     * Function to get current grade of child from gradepoint
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getCurrentGradeOfChildFromGradePoint($childId) {
        // getting grade of child
        $checkGarade = $this->_tblChildGradePoint->GetGradeofChild($childId);
        return $checkGarade; // return array;
    }

    /**
     * Function to get all subject of child
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayIterator
     */
    public function getChildAllSubject($childId) {
        $childSubjects = $this->_tblSubjectInfo->getChildSubjectsList($childId);
        return $childSubjects;
    }

    /**
     * Function to get challenge set for child
     * 
     * @param
     *        	$childId
     * @author suman khatri on 19th November 2013
     * @return ArrayObject
     */
    public function getChildSentChanllenge($childId) {
        $childChalangesSet = $this->_tblChildReqQues->getChildChallengesSet($childId);
        return $childChalangesSet;
    }

    /**
     * Function to get goal values for check learnig cutomization already added or not
     * 
     * @param
     *        	$childId
     * @author Dharemendra Mishra
     * @return ArrayObject
     */
    public function getChildGoals($childId) {
        return $this->_tblGoals->getChildGoals($childId);
    }

    /**
     * Function to check child name exist with parent or not
     * 
     * @param
     *        	childId
     * @author suman khatri on on 16th Jan 2014
     * @return $resUlt
     */
    public function checkChildNameWithParent($childId, $childName, $parId) {
        // check child name exist with parent or not
        $resUlt = $this->_tblChildInfo->checkChildName($childId, $childName, $parId);
        return $resUlt; // returns $resUlt
    }

    /**
     * Function to delete child's custom message
     * 
     * @param
     *        	$customMessage
     * @author suman khatri on 20th Jan 2014
     * @return result
     */
    public function deleteCustomMessage($customMessageId) {
        $res = $this->_tblCustomMessage->deleteCustomMessage($customMessageId); // delete data
        return $res; // returns result
    }

    /**
     * Function to get child's grade info
     * 
     * @param
     *        	$childId
     * @author suman khatri on 28th January 2014
     * @return ArrayIterator
     */
    public function getReportsGradeOfChild($childId) {
        // fetches all grades of child from which question was asked to the child
        $gradeInfo = $this->_tblChildReqQues->getQuestionAskedGradeOfChild($childId);
        return $gradeInfo; // returns array
    }

    /**
     * Function to get child's grade info
     * 
     * @param
     *      	$childId
     * @author suman khatri on 28th January 2014
     * @return ArrayIterator
     */
    public function checkLC($childId) {
        // fetches all grades of child from which question was asked to the child
        $lCData = $this->_tblGoals->fetchGoals($childId, 1);
        return $lCData; // returns array
    }

    /**
     * Function to get all lock times
     *
     * @param
     *      	Nill
     * @author suman khatri on 24th March 2014
     * @return ArrayIterator
     */
    public function getAllLockDeviceTime() {
        // fetches all lock device for times 
        $allData = $this->_tblLockDevicefor->getAllLockDeviceTime();
        $formatedData = array();
        $i = 0;
        foreach ($allData as $data) {
            $formatedData[$i] = $data['lock_device_for'];
            $i++;
        }
        return $formatedData; // returns array
    }

    /**
     * Function to get all question num data
     *
     * @param
     *      	Nill
     * @author suman khatri on 24th March 2014
     * @return ArrayIterator
     */
    public function getAllQuestionNumData() {
        // fetches all lock device for times
        $allData = $this->_tblQuestionNumAsktime->getQuestionNumData();
        $formatedData = array();
        $i = 0;
        foreach ($allData as $data) {
            $formatedData[$i] = $data['questionNum'];
            $i++;
        }
        return $formatedData; // returns array
    }

    /**
     * Function to get all ask time data
     *
     * @param
     *      	Nill
     * @author suman khatri on 24th March 2014
     * @return ArrayIterator
     */
    public function getAllAskTimeData() {
        // fetches all lock device for times
        $allData = $this->_tblQuestionNumAsktime->getAllAsktimeData();
        $formatedData = array();
        $i = 0;
        foreach ($allData as $data) {
            $formatedData[$i] = $data['asktime'];
            $i++;
        }
        return $formatedData; // returns array
    }

    /**
     * Function to get correspondent asktime
     *
     * @param
     *      	question numbet
     * @author suman khatri on 25th March 2014
     * @return ArrayObject
     */
    public function getCorrespondentAskTime($qNum) {
        // fetches record having number of question equal to $qNum
        $timeData = $this->_tblQuestionNumAsktime->getCorrespondentAsktime($qNum);
        return $timeData; //returns arrayObject
    }

    /**
     * Function to get default lock time
     *
     * @param
     *      	Nill
     * @author suman khatri on 31st March 2014
     * @return ArrayIterator
     */
    public function getDefaultLockDeviceTime() {
        // fetches all lock device for times
        $dData = $this->_tblLockDevicefor->getDefaultLockDeviceTime();
        $unlockTime = $dData['lock_device_for'];
        return $unlockTime; // returns array
    }

    function addChildSubject($childId, $gradeId, $subjectId, $isBibleOnly = FALSE, $noBibleQuestions = FALSE) {
        // block to add subject if grade changes on edit kid info
        // remove child's subject
        $this->removeChildSubject($childId);
        // remove child's sequence
        $this->_objectCategory->removeChildSequence($childId);
        // remove child's sequence track
        $this->_objectCategory->removeSequenceTrack($childId);

        // getting array of subjects related to grade
        if (empty($subjectId)) {
            $getSubjectList = $this->_objectCategory->getSubjectListGradeWise($gradeId, $isBibleOnly, $noBibleQuestions);
            //print_r($getSubjectList);die;
            if (isset($getSubjectList) && !empty($getSubjectList)) {
                // getting aaray of subjectIds
                $k = 0;
                //for($k = 0; $k < count ( $getSubjectList ['subject_list'] ); $k ++) {
                foreach ($getSubjectList ['subject_list'] as $subjectData) {
                    if (!empty($subjectData ['subject_id'])) {
                        $allDomainArray = $this->_objectCategory->getAllDomainsUsingGradeAndSubject($gradeId, $subjectData ['subject_id']);
                        $subjectId [$k]['subject_id'] = $subjectData['subject_id'];
                        if (count($allDomainArray) > 0) {
                            $subjectId [$k]['domainExist'] = 'Yes';
                            $subjectId [$k]['domainArray'] = $allDomainArray;
                        } else {
                            $subjectId [$k]['domainArray'] = null;
                        }
                        unset($allDomainArray);
                        $k++;
                    }
                }
            }
        }

        // Block to add child subject info
        $allCatArray = array();
        $maxCategoryCount = 0;

        $countSubjects = count($subjectId);

        for ($i = 0; $i < $countSubjects; $i ++) {

            // getting category ids for sequence accordingly

            $frameworkId = $subjectId [$i]['subject_id'];

            if ($subjectId [$i]['domainExist'] == 'Yes') {

                $domainArray = $subjectId [$i]['domainArray'];

                foreach ($domainArray as $domainId) {
                    // getting categories according to specified params
                    $category = $this->_objectCategory->getCategory(null, $frameworkId, $gradeId, $domainId);
                    $allCatArray[$frameworkId][$domainId] = $category;
                    $maxCategoryCount = (count($category) > $maxCategoryCount) ? count($category) : $maxCategoryCount;

                    // inserting subject and domain into DB
                    $this->insertChildSubject($childId, $frameworkId, $domainId);
                    //$addChildDoamin = $this->_objectchild->addDomainForChild ( $childId,$frameworkId,$domainId );
                }
            }
        }

        $category = $this->formatCategoryArrayAndGetSequence($allCatArray, $maxCategoryCount);
        // inserting sequence to DB for child
        $this->addSequenceUsingCategory($category, $childId, 1);

        // getting minimum sequence number of child
        $minsequencenumber = $this->_objectCategory->getMinSequenceOfChild($childId);
        // getting sequence number from $minsequencenumber
        $sequenceNumber = $minsequencenumber ['minsequenceid'];
        // inserting current sequence for child
        $this->_objectCategory->addOrUpdateSequenceOfChild($sequenceNumber, $childId, 'insert');
        // Block to add child subject info Ends/
        // end of block to add subject if grade changes on edit kid info

        return true;
    }

    function addSequenceUsingCategory($category, $childId, $j) {
        //$date = date("Y-m-d H:i:s"); // getting today date using 
        $date = todayZendDate();
        foreach ($category as $cat) {
            $categoryId = $cat ['category_id'];
            $addsequence = $this->_objectCategory->addSequenceForChild($childId, $categoryId, $j, $date);
            $j ++;
        }
        return $j;
    }

    function addlearnigCustomization($childId, $learnigCustomizationDataPost) {
        $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
        $addGoals = '';
        $formAction = '';
        $isInterrupt = '';
        $tyPe = '';
        $date = todayZendDate(); // getting today data time using function
        $dayOfWeek = date('w');
        if (empty($learnigCustomizationDataPost)) { // if check learnig data is empty or not
            $LCData = $this->_defaultLC->getLCData(); // get default learnig customization values to insert into db
            $quesAskTime = $LCData ['ask_question_after_every'];
            $noChances = $LCData ['number_of_chances'];
            $learningType = $LCData ['learning_type'];
            $weeklyGoal = $LCData ['weekly_goal'];
            $unlockTime = $LCData ['lock_device_for'];
            if ($learningType == 'part') {
                $unlockTime = 0;
            } else {
                $unlockTime = $unlockTime;
            }
            $addGoals = 'add';
            $add = 'add';
        } else { // else for geting data form the array
            $quesAskTime = $learnigCustomizationDataPost ['quesAskTime'];
            $noChances = $learnigCustomizationDataPost ['noChances'];
            $learningType = $learnigCustomizationDataPost ['radio-2-set'];
            $weeklyGoal = $learnigCustomizationDataPost ['weeklyGoal'];
            $unlockTime = $learnigCustomizationDataPost ['unlockTime'];
            $interruption = $learnigCustomizationDataPost ['checkboxInterruption'];
            if (isset($interruption) && $interruption == 'on') {
                $isInterrupt = 'Y';
            } else {
                $isInterrupt = 'N';
            }
            if ($learningType == 'part') {
                $unlockTime = 0;
            } else {
                $unlockTime = $unlockTime;
            }
            $tyPe = $learnigCustomizationDataPost ['type'];
            $formAction = $learnigCustomizationDataPost ['nextPage'];
            if ($formAction == 'add') {
                $addGoals = 'update';
            }
            $add = 'update';
        }

        // Block to add child goals info
        // adds child's goal info
        $addChildGoals = $this->addOrUpdateChildGoal($childId, $quesAskTime, $noChances, $weeklyGoal, $unlockTime, $add, $learningType, $isInterrupt);
        // block for weeekly Goals
        if (!empty($addGoals)) {
            //getting all paired devicelist of child
            $deviceData = $tblChildDeviceRel->getChildAllDevices($childId);
            //added/updated weekly goal if there is any paired device for child
            if (!empty($deviceData) && $deviceData != null) {
                $weekOfGoal = weekOfGoalValue($dayOfWeek, $weeklyGoal); // geeting days of week for goal
                $endDateWeekGoal = endDateWeekGoalVal($dayOfWeek); // geeting end date for weekly goal
                $addChildWeekGoals = $this->addOrUpdateChildWeeklyGoal($childId, $weekOfGoal, $date, $endDateWeekGoal, $addGoals, $weeklyGoal);
            }
        }

        if ($formAction == 'add') { // if to check action is add or edit for add device into db or update.
            if ($tyPe == 'edit') {
                $confirmMessage = "Child learning customization info updated successfully";
            } else {
                $confirmMessage = "Child learning customization info added successfully";
            }
            return $confirmMessage;
        } else if ($formAction == 'edit') {
            $confirmMessage = "Child learning customization info updated successfully";
            return $confirmMessage;
        } else {
            return true;
        }
    }

    // end private learnig cutomization page to save data.

    /**
     * Function to get child's weekly goal
     * 
     * @param
     *        	$childId
     * @author suman khatri on 10th April 2014
     * @return ArrayIterator
     */
    public function getChildWeeklyGoal($childId) {
        //getting weekly goal data
        $result = $this->_tblWeekGoals->getWeekGoalData($childId);
        return $result; // return result;
    }

    /**
     * Function to get count of parent having child count
     * 
     * @param
     *        	NILL
     * @author suman khatri on 14th April 2014
     * @return ArrayObject
     */
    public function getAllParentwithChildCount() {
        // get count of parent having child count
        $allData = $this->_tblChildInfo->getAllParentwithChildCount();
        // get count of parent having no child
        $allDataWithNochild = $this->_objectParent->getAllParentwithNoChild();
        $arrayA = array();
        $i = 0;
        $allData = array_map('current', $allData);
        $allDataWithNochild = array_map('current', $allDataWithNochild);
        $allData = array_count_values($allData);
        $parentwithNoChild = count($allDataWithNochild);
        $formatedArray = array();
        $formatedFinalArray = array();
        $arrayD = array();
        if ((!empty($allData) && $allData != null) || (!empty($parentwithNoChild) && $parentwithNoChild != 0)) {
            $formatedArray[0]['id'] = '';
            $formatedArray[0]['label'] = '# of children';
            $formatedArray[0]['pattern'] = '';
            $formatedArray[0]['type'] = 'number';
            $formatedArray[1]['id'] = '';
            $formatedArray[1]['label'] = '# of parents';
            $formatedArray[1]['pattern'] = '';
            $formatedArray[1]['type'] = 'number';
            $formatedFinalArray['cols'] = $formatedArray;
            unset($formatedArray);
            $i = 0;
            //   if($parentwithNoChild > 0){
            $formatedArray[0]['v'] = 0;
            $formatedArray[0]['f'] = 'No Children';
            $formatedArray[1]['v'] = $parentwithNoChild;
            $formatedArray[1]['f'] = null;
            $arrayD[$i]['c'] = $formatedArray;
            unset($formatedArray);
            $i++;
            //    }
            foreach ($allData as $key => $value) {
                $formatedArray[0]['v'] = $key;
                $formatedArray[0]['f'] = $key . ' Children' . ($key > 1 ? 's' : '');
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

    public function getTotalFinnyChild() {
        $data = $this->_objectDevice->getAllChildWithConfiguredDevice();
        $dataLite = $this->_tblChildInfo->getTotalFinnyLiteChild();
        $totalFinnyKid = (int) $data;
        $totalFinnyLiteKid = (int) $dataLite['totalFinnyLiteKid'];
        if ((!empty($totalFinnyLiteKid) && $totalFinnyLiteKid != null) || (!empty($totalFinnyKid) && $totalFinnyKid != null)) {
            $cols = array(
                'cols' => array(
                    array('id' => '', 'label' => 'Finny Users', 'pattern' => '', 'type' => 'string'),
                    array('id' => '', 'label' => 'Count', 'pattern' => '', 'type' => 'number')
                ),
                'rows' => array(
                    array('c' => array(
                            array('v' => 'Finny Users', 'f' => null),
                            array('v' => $totalFinnyKid, 'f' => null)
                        )),
                    array('c' => array(
                            array('v' => 'Finny Lite Users', 'f' => null),
                            array('v' => $totalFinnyLiteKid, 'f' => null)
                        ))
                )
            );
            return json_encode($cols);
        } else {
            return false;
        }
    }

    /**
     * @desc Function to get child and parent info grade wise
     * @param $gradeId
     * @author suman khatri on 1st May 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWise($gradeId, $subjectId = null) {
        $childInfo = $this->_tblChildInfo->getChildParentInfoGradeWise(
                $gradeId, $subjectId
        );
        return $childInfo;
    }

    /**
     * @desc Function to get child subject list
     * @param gardeId
     * @author suman khatri on 21 June 2014
     * @return array
     */
    public function getChildSubjectListWithDomain($childId, $gradeId) {
        //getting subject list grade wise 
        $getSubjectList = $this->_tblChildSubject->getChildSubjectListWithDomain(
                $childId, $gradeId
        ); //fetches subject list of grade
        $subjectDomainArray = array();
        //getting all domain related to the subject and grade
        foreach ($getSubjectList as $subjectData) {
            $subjectDomainArray[$subjectData['subject_id']]['subject_id'] = $subjectData['subject_id'];
            $subjectDomainArray[$subjectData['subject_id']]['subject_name'] = $subjectData['subject_name'];
            $subjectDomainArray[$subjectData['subject_id']]['domainArray']
                    [$subjectData['domain_id']]['domain_id'] = $subjectData['domain_id'];
            //$subjectDomainArray[$subjectData['subject_id']][$subjectData['domain_id']]['code'] = $subjectData['code'];
            // $subjectDomainArray[$subjectData['subject_id']][$subjectData['domain_id']]['name'] = $subjectData['name'];
        }
        $subCount = count($subjectDomainArray); //count of subjects
        //assigns both value to an array
        $subjectListFinal = array(
            'subject_list' => $subjectDomainArray,
            'total_subject' => $subCount
        );
        return $subjectListFinal;
    }

    /**
     * @desc Function to get child and parent info grade wise having specified domain ID
     * @param $gradeId,$subjectId,$domainId
     * @author suman khatri on 24th June 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWiseHavingSpecifiedDomian(
    $gradeId, $subjectId, $domainId
    ) {
        $childIds = $this->_tblChildInfo
                ->getChildParentInfoGradeWiseHavingSpecifiedDomian(
                $gradeId, $subjectId, $domainId
        );
        return $childIds;
    }

    /**
     * @desc Function to get child subject list
     * @param $childId,$subjectId
     * @author suman khatri on 25th June 2014
     * @return array
     */
    public function getDomainListSubjectAndChildWise($childId, $subjectId) {
        //getting subject list grade wise 
        $domainList = $this->_tblChildSubject->getDomainListSubjectAndChildWise(
                $childId, $subjectId
        ); //fetches subject list of grade        
        return $domainList;
    }

    /**
     * @desc Function to get child and parent info grade wise not having specified domain ID
     * @param $gradeId,$subjectId,$domainId
     * @author suman khatri on 24th June 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWiseNotHavingSpecifiedDomian($gradeId, $subjectId, $domainId, $childIds
    ) {
        $childInfo = $this->_tblChildInfo
                ->getChildParentInfoGradeWiseNotHavingSpecifiedDomian(
                $gradeId, $subjectId, $domainId, $childIds
        );
        return $childInfo;
    }

    /**
     * @desc Function to get child goal
     * @param $childId
     * @author Suman Khatri on 9th October 2014
     * @return Array
     */
    public function getChildGoal($childId) {
        //getting goal of child 
        $goals = $this->_tblGoals->fetchGoals($childId);
        return $goals; // return array
    }

    /**
     * @desc Function to add/update kids basic info
     * @param $kidFname,$kidLname,$sexOfChild,$dateOfBirth,$schoolName,
      $gradeId,$cgpa,$childId,$parentId
     * @author Suman Khatri on 9th October 2014
     * @return Array
     */

    public function addOrUpdateKidsBasicInfo($kidFname, $sexOfChild, $dateOfBirth, $schoolName, $gradeId, $cgpa, $childId, $parentId, $coppa_required) {
        //creates child basic info array
        $childName = trim($kidFname);
        $childBasicInfo = array(
            'parent_id' => $parentId,
            'name' => $childName,
            'firstname' => $childName,
            'dob' => $dateOfBirth,
            'gender' => $sexOfChild,
            'school_name' => $schoolName,
            'gpa' => $cgpa,
            'grade_id' => $gradeId,
            'created_date' => date('Y-m-d H:i:s')
        );
        if (!empty($childId) && $childId != null) {
            // updates data into DB
            $childId = $this->_tblChildInfo->updateChildInfo($childBasicInfo, $childId);
        } else {
            $childBasicInfo['coppa_required'] = $coppa_required;
            if(!$coppa_required) {
                $childBasicInfo['track_location'] = 1;
            }
            // insert data into DB
            $childId = $this->_tblChildInfo->addChild($childBasicInfo);
        }
        return $childId; //returns childId
    }

    /**
     * @desc Function to check child's full name 
     * @param $childId,$childName,$parId
     * @author suman khatri on October 01 2014
     * @return result
     */
    public function checkChildFullName($childId, $childName, $parId) {
        //calling function and returning result
        return $this->_tblChildInfo->checkChildFullName(
                        $childId, $childName, $parId
        );
    }

    /**
     * @desc Function to get child info using parent Id/child Id
     * @param $parentId,$childId
     * @author suman khatri on October 09 2014
     * @return arrayObject
     */
    public function getChildrensAllInfoUsingParentIdOrChildId($parentId, $childId) {
        //getting childInfo 
        $childInfo = $this->_tblChildInfo
                ->getChildrensAllInfoUsingParentIdOrChildId($parentId, $childId);
   
        return $childInfo; //returns childInfo
    }

    /**
     * @desc Function to format childArray
     * @param $parentId
     * @author suman khatri on October 09 2014
     * @return arrayIterator
     */
    public function formateChildArray($childsInfo) {
        $dataChilderList = array();
        $i = 0;
        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();       
        foreach ($childsInfo as $dataChildren) {
            $childId = $dataChildren['child_id'];
            $dataChilderList[$i]['child_id'] = $dataChildren['child_id'];
            $dataChilderList[$i]['child_f_name'] = html_entity_decode($dataChildren['firstname']);
            $dataChilderList[$i]['child_l_name'] = html_entity_decode($dataChildren['lastname']);
            $dataChilderList[$i]['gradeId'] = $dataChildren['grade_id'];
            $dataChilderList[$i]['gender'] = !empty($dataChildren['gender']) ? $dataChildren['gender'] : '';
            $dataChilderList[$i]['time'] = $dataChildren['question_popup_time'];
            $dataChilderList[$i]['no_of_questions'] = $dataChildren['number_of_questions'];
            $dataChilderList[$i]['unlock_time'] = $dataChildren['unlock_time'];
            $dataChilderList[$i]['goal_type'] = $dataChildren['learning_type'];
            $dataChilderList[$i]['is_interrupt'] = $dataChildren['is_interrupt'];
            $dataChilderList[$i]['weekly_points'] = $dataChildren['weekly_points'];
            $dataChilderList[$i]['total_points'] = $dataChildren['points'];
            if (!empty($dataChildren['dob'])) {
                $dateOfBirth = date('m/d/Y', strtotime($dataChildren['dob']));
            } else {
                $dateOfBirth = null;
            }
            $dataChilderList[$i]['dob'] = $dateOfBirth;
            $dataChilderList[$i]['gpa'] = $dataChildren['gpa'];
            $dataChilderList[$i]['school_name'] = html_entity_decode($dataChildren['school_name']);
            if (!empty($dataChildren['message']) && $dataChildren['message'] != null) {
                $dataChilderList[$i]['custom_message'] = $dataChildren['message'];
            } else {
                $dataChilderList[$i]['custom_message'] = "Question from finny!";
            }
            //getting image path for child
            $imagePath = $serverUrl->serverUrl() . $baseUrl->baseUrl('/images/no-image-child.png');
            if(!empty($dataChildren['image'])) {
                $imagePath = AWS_S3_URL . 'child/' . $dataChildren['image'];
            }
            $dataChilderList[$i]['child_image'] = $imagePath;
            
            $dataChilderList[$i]['coppa_required'] = $dataChildren['coppa_required'];
            $dataChilderList[$i]['coppa_accepted'] = $dataChildren['coppa_accepted'];
            $dataChilderList[$i]['track_location'] = $dataChildren['track_location'];
            $dataChilderList[$i]['avatar'] = $dataChildren['avatar'];
            
            $i++;
        }
        return $dataChilderList; //returns all childrens info
    }

    /**
     * @desc Function to send puch on add or upate kid info
     * @param $parentId,$childIinfo,$type
     * @author suman khatri on October 12 2014
     * @return arrayIterator
     */
    public function sendPushOnAddOrUpdateKid($parentId, $childIinfo, $type, $childId = null, $deviceId = null) {
        $devicesInfo = $this->_objectParent->getAllregisteredDeviceInfoOfParent(
                $parentId, $deviceId
        );
        $result = TRUE;
        foreach ($devicesInfo as $deviceData) {
            if ($deviceData['registered_id']) {
                $registeredId = $deviceData['registered_id'];
                $deviceStatus = 'kid info';
                if ($type == 'add') {
                    $deviceMessage = 'add kid';
                }
                if ($type == 'edit') {
                    $deviceMessage = 'update kid';
                }
                $childArray['status_code'] = STATUS_SUCCESS;
                $childArray['children_list'] = $childIinfo;
                $sendNotificationData = array(
                    'process_code' => $deviceMessage,
                    'message' => $deviceStatus,
                    'data' => $childArray
                );
                if (!empty($childId) && $childId != null) {
                    $sendNotificationData['childId'] = $childId;
                }
                $gcm = new My_GCM ();
                $result = $gcm->send_notification(array(
                    $registeredId
                        ), $sendNotificationData);
            }
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc Function to get child image path
     * @param $image,$gender
     * @author suman khatri on October 12 2014
     * @return iamge path
     */
    private function _getImagePathOfChild($image, $gender) {
        //checking if image is exist or not
        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $imageURL = $serverUrl->serverUrl() . $baseUrl->baseUrl('/images/no-image-child.png');
        if ($image != '') {
            //if exist then set image
            $imageURL = AWS_S3_URL . 'child/' . $image;
        }
        //setting imagepath
        return $imageURL; //return image path
    }

    /**
     * @desc Function to get child info array
     * @param int $childId id of child
     * 
     * @author suman khatri on October 29 2014
     * @return array
     */
    public function getChildInfoArray($childId) {
        $childrenList = $this->getChildrensAllInfoUsingParentIdOrChildId(
                null, $childId
        );
        //formatting child Array
        $childInfoArray = $this->formateChildArray(
                $childrenList
        );
        return $childInfoArray;
    }

    /**
     * @desc Function to valiate child and device info 
     * @param int $childId id of child
     * @param int $deviceId id of device 
     * 
     * @author suman khatri on October 30 2014
     * @return array
     */
    public function validateChildandDeviceInfo($childId, $deviceId) {
        //creats object for class ChildDeviceRelationInfo
        $tblchildDeviceInfo = new
                Application_Model_DbTable_ChildDeviceRelationInfo();
        if ($childId == null || empty($childId)) {
            return "Child id can't be empty";
        } else if (!empty($childId)) {
            //getting child device info
            $childDeviceRelation = $tblchildDeviceInfo
                    ->checkDeviceExistOrNotInChildDeviceRelation($deviceId, $childId);
            if (empty($childDeviceRelation) || $childDeviceRelation == null) {
                return "child is not associated with this device";
            }
        }
        return null;
    }

    public function addWeeklyGoalForChild($childId, $parId, $weeklyGoal) {
        $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
        $dayOfWeek = date('w');
        //getting all paired devicelist of child
        $deviceData = $tblChildDeviceRel->getChildDevices($childId, $parId);
        //added/updated weekly goal if there is any paired device for child
        if (!empty($deviceData) && $deviceData != null) {
            $weekOfGoal = weekOfGoalValue($dayOfWeek, $weeklyGoal); // geeting days of week for goal
            $endDateWeekGoal = endDateWeekGoalVal($dayOfWeek); // geeting end date for weekly goal
            $addChildWeekGoals = $this->addOrUpdateChildWeeklyGoal($childId, $weekOfGoal, todayZendDate(), $endDateWeekGoal, 'add', $weeklyGoal);
        }
    }

    public function getChildsTotalPoints($childId) {
        $data = $this->_tblChildGradePoint->getChildPointsofCurrentGrade($childId);
        if (count($data)) {
            return $data[0]['points'];
        }

        return 0;
    }

    public function sendPushToAllDevices($childId, $data) {
        
        $childInfo = $this->getChildBasicInfo($childId);

        $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
        $devicesInfo = $tblChildDeviceRel->getChildAssociateDevices($childId, $childInfo['parent_id']);
        foreach ($devicesInfo as $deviceData) {
            $gcm = new My_GCM ();
            $gcm->send_notification(array($deviceData['registered_id']), $data);
        }

        return TRUE;
    }

    public function getAssociatedDecviceCount($childId) {
        $childInfo = $this->getChildBasicInfo($childId);
        $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
        $devicesInfo = $tblChildDeviceRel->getChildAssociateDevices($childId, $childInfo['parent_id']);
        return count($devicesInfo);
    }

    public function formatCategoryArrayAndGetSequence($allCatArray, $maxCategoryCount) {
        $category = array();

        for ($counter = 0; $counter < $maxCategoryCount; $counter++) {
            foreach ($allCatArray as $subCatArray) {
                foreach ($subCatArray as $subArray) {
                    if (isset($subArray[$counter])) {
                        $category[] = $subArray[$counter];
                    }
                }
            }
        }

        return $category;
    }

    /**
     * Check is COPPA consent is accepted or not
     * @param Int $childId
     * @return boolean
     */
    public function isCoppaAccepted($childId)
    {
        return $this->_tblChildInfo->isCoppaAccepted($childId);
    }

    public function isValidReminderToken($childId, $token)
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        return $dbCoppaReminder->isValidReminderToken($childId, $token);
    }

    public function createCoppaReminderToken($childId)
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        return $dbCoppaReminder->createReminderToken($childId);
    }
    
    public function removeCoppaReminder($childId)
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        $dbCoppaReminder->delete(array('child_id' => $childId));
    }

    public function resetCoppaReminder($childId)
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        $dbCoppaReminder->resetReminder($childId);
    }

    public function getChildListToRemind()
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        return $dbCoppaReminder->getChildListToRemind();
    }

    public function incrementReminderCount($childId)
    {
        $dbCoppaReminder = new Application_Model_DbTable_ChildCoppaReminder();
        return $dbCoppaReminder->incrementReminderCount($childId);
    }
    
    /**
     * @desc Function to delete child
     * @param int  childId
     * @author Abhinav Bhardwaj on January 02 2016
     * @return int responce
     */
    public function deleteChild($childId){
          $responce         =   $this->_tblChildInfo->deleteData($childId); 
          return $responce;
    }    
}