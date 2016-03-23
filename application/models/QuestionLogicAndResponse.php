<?php

class Application_Model_QuestionLogicAndResponse extends Zend_Loader_Autoloader {
    /* defined all object variables that are used in entire class
     */

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        
    }
    
    private function _getcurrentsequence($childId, $gradId) {
        $tblsequence = new Application_Model_DbTable_ChildQuestionSequence ();
        $tblsequenceTrack = new Application_Model_DbTable_ChildQuestionSequenceTrack ();
        // getting current sequence
        $sequenceInfo = $tblsequenceTrack->getCurrentsequencenumber($childId);
        $currentSequence = $sequenceInfo ['current_sequence_number'];
        // getting max sequence number from seqquence
        $maxsequenceInfo = $tblsequence->getMaxsequenceHavingQuestion($childId, $gradId);
        $maxSequence = $maxsequenceInfo ['maxsequenceid'];
        // updating the sequence track data
        if ($currentSequence == $maxSequence) {
            $minsequenceInfo = $tblsequence->getMinsequenceHavingQuestion($childId, $gradId);
            $nextsequende = $minsequenceInfo ['minsequenceid'];
        } else {
            $nextsequenceInfo = $tblsequence->GetNextSeuencesHavingQuestion($currentSequence, $childId, $gradId);
            $nextsequende = $nextsequenceInfo ['nextsequenceid'];
        }
        $sequencetrackdata = array(
            'current_sequence_number' => $nextsequende
        );
        $updateseqtrack = $tblsequenceTrack->updateData($sequencetrackdata, $childId);
        return $currentSequence;
    }

    private function _getcurrentsequenceForUnaskedQuestion($childId, $gradId, $askedquestion) {
        $tblsequence = new Application_Model_DbTable_ChildQuestionSequence ();
        $tblsequenceTrack = new Application_Model_DbTable_ChildQuestionSequenceTrack ();
        // getting current sequence
        $sequenceInfo = $tblsequenceTrack->getCurrentsequencenumber($childId);
        $currentSequence = $sequenceInfo ['current_sequence_number'];
        // getting max sequence number from seqquence
        $maxsequenceInfo = $tblsequence->getMaxsequenceHavingUnaskedQuestion($childId, $gradId, $askedquestion);
        $maxSequence = $maxsequenceInfo ['maxsequenceid'];
        // updating the sequence track data
        if ($currentSequence == $maxSequence) {
            $minsequenceInfo = $tblsequence->getMinsequenceHavingUnaskedQuestion($childId, $gradId, $askedquestion);
            $nextsequende = $minsequenceInfo ['minsequenceid'];
        } else {
            $nextsequenceInfo = $tblsequence->GetNextSeuencesHavingUnaskedQuestion($currentSequence, $childId, $gradId, $askedquestion);
            $nextsequende = $nextsequenceInfo ['nextsequenceid'];
        }
        $sequencetrackdata = array(
            'current_sequence_number' => $nextsequende
        );
        $updateseqtrack = $tblsequenceTrack->updateData($sequencetrackdata, $childId);
        return $currentSequence;
    }

    // function to get asked question and remaining question of the current sequence
    public function getquestion($childId, $gradId) {
        $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        $childQuestion = new Application_Model_DbTable_ChildQuestion ();
        $tblsequence = new Application_Model_DbTable_ChildQuestionSequence ();
        $askedquestion = '';
        $allsequence = $tblsequence->GetAllSeuencesHavingQuestion($childId, $gradId);
        $dummy['sequence_number'] = 0;
        array_push($allsequence, $dummy);
        if (count($allsequence) != 0 && !empty($allsequence)) {
            $seq = '';

            $allsequence = array_map('current', $allsequence);
            $seq = implode(",", $allsequence); //echo $seq;die;
            $qexistallseq = $childQuestion->GetExistQuestionforallsequence($seq, $askedquestion, $childId, $gradId);
            if (!empty($qexistallseq)) {
                try {
                    // getting asked question info
                    $askedQuestionInfo = $tblQuestionRequest->getAskedQuestionToFetchQuestionIds($childId, $seq, $gradId);
                    $askedQuestionInfo = array_map('current', $askedQuestionInfo);
                    $askedquestion = implode(",", array_filter($askedQuestionInfo));
                    $unaskedqexistallseq = $childQuestion->GetExistQuestionforallsequence($seq, $askedquestion, $childId, $gradId);
                    try {
                        if (count($unaskedqexistallseq) != 0) {
                            $allsequenceHvaingUnaskedQuestion = $tblsequence->GetAllSeuencesHavingUnAskedQuestion($childId, $gradId, $askedquestion);
                            array_push($allsequenceHvaingUnaskedQuestion, $dummy); //print_r($allsequenceHvaingUnaskedQuestion);die;
                            foreach ($allsequenceHvaingUnaskedQuestion as $s) {
                                $currentSequence = $this->_getcurrentsequenceForUnaskedQuestion($childId, $gradId, $askedquestion);
                                $qexist = $childQuestion->GetExistQuestion($currentSequence, $askedquestion, $childId, $gradId);
                                if (!empty($qexist)) {
                                    $questionInfo = $qexist[0];
                                    return $questionInfo;
                                    exit();
                                } else {
                                    // fi unasked question is not exist then continue to the loop
                                    continue;
                                }
                            }
                        } else {
                            foreach ($allsequence as $s) {
                                $currentSequence = $this->_getcurrentsequence($childId, $gradId);
                                $questionInfo = $childQuestion->GetQuestionRandomly($currentSequence, $childId, $gradId);
                                if (!empty($questionInfo)) {
                                    return $questionInfo;
                                    exit();
                                } else {
                                    // fi unasked question is not exist then continue to the loop
                                    continue;
                                }
                            }
                        }
                    }catch (Exception $e) {
                        return $e->getMessage();
                        exit();
                    }
                } catch (Exception $e) {
                    return $e->getMessage();
                    exit();
                }
            } else {
                return "no question";
                exit();
            }
        } else {
            return "no question";
            exit();
        }
    }
    
    /**
     * ****function for get category title
     *
     * @param
     *        	category id
     * @return array **
     */
    public function getCategoryTitle($categoryId) {
        $tblBlqCategory = new Application_Model_DbTable_QuestionCategories ();
        $categoryList = $tblBlqCategory->getTitleExplatation($categoryId);
        return $categoryList;
    }

    /**
     * ***********End Category*************
     */

    /**
     * *******************function for get custom message if it's have***************
     */
    public function getCustomMessage($requestId) {
        $tblCustomMessage = new Application_Model_DbTable_CustomMessage ();
        $customMeage = $tblCustomMessage->getCustomMeassage($requestId);
        return $customMeage;
    }

    /**
     * ******************end*********************
     */

    /**
     * *******************function for update question response in child_question table***************
     */
    public function updateQuestionResponse($requestId, $answerByChild,$deviceId = null) {
        $tblReqQuestion = new Application_Model_DbTable_RequestQuestion ();
        $updateChildQuestion = array(
            'answered_option_id' => $answerByChild,
            'modified_date' => todayZendDate()
        );
        if(!empty($deviceId)){
            $updateChildQuestion['child_device_id'] = $deviceId;
        }
        $resultData = $tblReqQuestion->updatQuestionResponce($updateChildQuestion, $requestId);
        return $resultData;
    }

    /**
     * ******************end*********************
     */

    /**
     * *******************function for update child device location***************
     */
    public function updateChildDeviceLocation($longitude, $latitude, $childDeviceId) {
        $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo ();
        $updateData = array(
            'longitude' => $longitude,
            'latitude' => $latitude,
            'modified_date' => todayZendDate()
        );
        $res = $tblDeviceInfo->updateDeviceInfo($updateData,$childDeviceId);
        return $res;
    }

    /**
     * ******************end*********************
     */

    /**
     * *******************function for update request data***************
     */
    public function updateRequestData($responseTime, $response_points, $transactionType, $points, $latitude, $longitude, $requestId) {
        $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        $updaterequestData = array(
            'response_date' => todayZendDate(),
            'device_response_time' => $responseTime,
            'response_points' => $response_points,
            'points_type' => $transactionType,
            'points' => $points,
            'latitude' => $latitude,
            'longitude' => $longitude
        );
        $whererequestUpadte = "request_id = $requestId";
        $updateReq = $tblQuestionRequest->update($updaterequestData, $whererequestUpadte);
        return $updateReq;
    }

    /**
     * ******************end*********************
     */
    /*     * *********************function to get performance********************** */
    public function getPerformance($requestId) {
        $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        $QuestionReferenceData = $tblQuestionRequest->getReferenceIdUsingRequestId($requestId);
        $referId = $QuestionReferenceData['reference_request_id'];
        if (empty($referId) || $referId == null) {
            $referId = $requestId;
        }
        $totalQuestion = $tblQuestionRequest->getTotalQuestionUsingRequestId($referId);
        $right = $totalQuestion['totalCorrect'];
        $wrong = $totalQuestion['totalIncorrect'];
        $total = $right + $wrong;
        $performance = ($right / $total) * 100;
        if ($performance < 60) {
            $result = "Y";
        } else {
            $result = "N";
        }
        return $result;
    }  
    
    // function to get trophy detail
    public function gettrophydata($requestId, $gradId, $childId, $existingPoints, $childDeviceId, $parentId) {
        $tblReqQuestion = new Application_Model_DbTable_RequestQuestion ();
        $tblchildTrophy = new Application_Model_DbTable_ChildTrophy ();
        $tblsubtrophy = new Application_Model_DbTable_Trophiessub ();
        $tbltrophy = new Application_Model_DbTable_Trophies ();
        $tblChildInfo = new Application_Model_DbTable_ChildInfo ();
        $tblParentInfo = new Application_Model_DbTable_ParentInfo ();
        $objParent = new Application_Model_Parents();
        $objTrophy = new Application_Model_Trophy();
        
        try {
            $childInfo = $tblChildInfo->getChildInfo($childId);
            $childName = $childInfo->name;
            $parId = $childInfo->parent_id;
            $whereparent = "parent_id = $parId";
            $parentInfo = $tblParentInfo->fetchRow($whereparent);
            $userId = $parentInfo ['user_id'];
            // *******************to give trophy to the child*******************//
            // ********************subject wise trophy*******************//
            // get question_id for reqtest
            $qIdinfo = $tblReqQuestion->GetQuestionId($requestId);
            $qId = $qIdinfo ['question_id'];
            // get subject of the question
            $subjectIDandPoints = $this->getSubjectIdAndPoint($qId,$gradId, $childId);
            $subjectId = $subjectIDandPoints['subjectId'];
            $subpoint = $subjectIDandPoints['subjectPoints'];
            
            $subjecttropyInfo = $tblsubtrophy->GetTrophyForsubjectPoints($subjectId, $subpoint, $gradId);
            $childTrophyIds = '';
            foreach ($subjecttropyInfo as $tr) {
                if ($subpoint >= $tr['points']) {
                    $checktrophyexist = $tblchildTrophy->getExisttrophy($childId, $tr['title'], 
                        $tr['description'], $tr['image'], $subjectId, $gradId
                    );
                    if ($checktrophyexist != true) {
                        $addsubtrophy = $objTrophy->addTrophyForKid($tr, 'TS', $childId, $gradId);
                        if ($addsubtrophy != '') {
                            $resnotifis = $objParent->addParentNotificationForTrophy(
                                $subjectId, $tr['points'], null, $userId, $tr['title'], 
                                $childDeviceId, $childName, $childId
                            );
                            if ($childTrophyIds == '') {
                                $childTrophyIds = "'" . $addsubtrophy . "'";
                            } else {
                                $childTrophyIds = $childTrophyIds . ",'" . $addsubtrophy . "'";
                            }
                        }
                    }
                }
            }
            //**********************subject wise trophy end**************************//
            //*********************total number wise trophy**************************//
            $numbertropyInfo = $tbltrophy->GetTrophyFortotalPoints($existingPoints);
            foreach ($numbertropyInfo as $ntr) {
                if ($existingPoints >= $ntr['points']) {
                    $checktrophyexist = $tblchildTrophy->getExisttrophyWithoutGrade($childId, $ntr['title'], $ntr['description'], $ntr['image'], $subjectId = 0);
                    if ($checktrophyexist != true) {
                        $addtotaltrophy = $objTrophy->addTrophyForKid($ntr, 'TO', $childId, $gradId);
                        if ($addtotaltrophy != '') {
                            $resnotifis = $objParent->addParentNotificationForTrophy(
                                null, $ntr['points'], null, $userId, $ntr['title'], 
                                $childDeviceId, $childName, $childId
                            );
                            if ($childTrophyIds == '') {
                                $childTrophyIds = "'" . $addtotaltrophy . "'";
                            } else {
                                $childTrophyIds = $childTrophyIds . ",'" . $addtotaltrophy . "'";
                            }
                        }
                    }
                }
            }
            if (!empty($childTrophyIds)) {
                $childTrophy = $tblchildTrophy->gettrophyusingId($childTrophyIds);
                $trophyarray = $objTrophy->sendPushOfTrophy($childTrophy, null, null, 'child trophy',$parentId);
            }
            if (!empty($trophyarray)) {
                return $trophyarray;
            } else {
                $trophyarray = null;
                return $trophyarray;
            }
            // **********************number wise trophy end***************************//
            // *******************end to give trophy to the child*********************//
        } catch (Exception $e) {
            $newarray ['status_code'] = STATUS_SYSTEM_ERROR;
            $newarray ['message'] = $e->getMessage();
            echo json_encode($newarray);
            die();
        }
    }
    
    public function addQuestionRequest($type,$childId,$gradId,$wager_points,$turn,$refernceId){
        $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        $data = array(
            'request_type' => $type,
            'child_id' => $childId,
            'grade_id' => $gradId,
            'request_date' => date('Y-m-d H:i:s')
        );
        if(!empty($wager_points)){
            $data['response_points'] = $wager_points;
            $data['points'] = $wager_points;
        }
        if(!empty($turn)){
            $data['turn'] = $turn;
        }
        if(!empty($refernceId)){
            $data['reference_request_id'] = $refernceId;
        }
        $res = $tblQuestionRequest->AddRequest($data);
        return $res;
    }
    
    public function createOptionArray($qid){
        $tblOptions = new Application_Model_DbTable_QuestionOptions ();
        $options = $tblOptions->getOptionforQuestion($qid);
        $o = 0;
        foreach ($options as $opt) {
            $optionArray [$o] ['option_id'] = $opt ['question_option_id'];
            if (!empty($opt ['option_equation_image_name']) && $opt ['option_equation_image_name'] != null) {
                $optionArray [$o] ['option_value'] = My_Functions::generateAndroidView($opt ['option_equation_image_name'], $opt ['option_equation']);
            } else {
                $optionArray [$o] ['option_value'] = $opt ['option'];
            }
            if($opt ['answer'] == 'Y'){
                $rightOptionId = $opt ['question_option_id'];
                 if (!empty($opt ['option_equation_image_name']) && $opt ['option_equation_image_name'] != null) {
                    $rightOption = My_Functions::generateAndroidView($opt ['option_equation_image_name'], $opt ['option_equation']);
                } else {
                    $rightOption = $opt ['option'];
                }
            }
            $o ++;
        }
        $returnArray = array(
            'optionArray' => $optionArray,
            'rightOptionId' => $rightOptionId,
            'rightOption' => $rightOption
        );
        return $returnArray;
    }
    
    public function addChildQuestion($deviceId,$qid,$requestId,$points){
        $tblChildQuestion = new Application_Model_DbTable_RequestQuestion ();
        $addChildQuestion = array(
            'child_device_id' => $deviceId,
            'question_id' => $qid,
            'answered_option_id' => 0,
            'request_id' => $requestId,
            'points_offered' => $points,
            'created_date' => date('Y-m-d H:i:s')
        );
        $reschildQuestion = $tblChildQuestion->AddQuestion($addChildQuestion);
        return $reschildQuestion;
    }
    
    
    public function createArrayForQestionInfo($requestId,$questionInfo,$existingPoints,$points,$type,$childId,$requestType = null,$gradeId){
        $optionArrayData = $this->createOptionArray($questionInfo ['bal_question_id']);
        //getting subject is and points
        // get subject of the question
        $subjectIDandPoints = $this->getSubjectIdAndPoint($questionInfo ['bal_question_id'],$gradeId, $childId);
        $subjectId = $subjectIDandPoints['subjectId'];
        $subpoint = $subjectIDandPoints['subjectPoints'];

        
        
        if($requestType != 'oneshot'){
            $newarray ['status_code'] = STATUS_SUCCESS;
        }
        if($requestType != 'demo'){
            $newarray ['request_id'] = $requestId;
        }
        if (!empty($questionInfo ['question_equation_images']) && $questionInfo ['question_equation_images'] != null) {
            $newarray ['question'] = My_Functions::generateAndroidView($questionInfo ['question_equation_images'], $questionInfo ['question_display']);
        } else {
            $newarray ['question'] = $questionInfo ['question'];
        }
        $newarray ['subject_id'] = $subjectId;
        $newarray ['subject_point'] = $subpoint;
        $newarray ['right_option_id'] = $optionArrayData ['rightOptionId'];
        $newarray ['right_option'] = $optionArrayData ['rightOption'];
        $optionArray = $optionArrayData['optionArray'];
        shuffle($optionArray);
        $newarray ['options'] = $optionArray;
        if($requestType != 'demo'){
            $newarray ['total_points'] = $existingPoints;
            if($type == 'wagor'){
                $newarray ['wager_points'] = $points;
            } else if ($type == 'quize' || $type == 'oneshot') {
                $newarray['points_offered'] = $points;
                $newarray ['points_deduct'] = round($points / 4);
            }
            $customMessage = $this->getCustomMessage($requestId);
            $newarray['custom_message'] = $customMessage['message'];
            $parentImage = $this->getParentImage($childId);
                
            $newarray['parent_image'] = $parentImage;
        }
        
        $categoryData = $this->getCategoryTitle($questionInfo ['category_id']);
        $newarray ['category_title'] = $categoryData [0] ['subject_name'];
        if(strtolower($newarray['category_title']) == 'math.content') {
            $newarray ['category_title'] = 'Math';
        }
        if (!empty($questionInfo ['answer_explanation_image_name']) && $questionInfo ['answer_explanation_image_name'] != null) {
            $newarray ['explanation'] = My_Functions::generateAndroidView($questionInfo ['answer_explanation_image_name'], $questionInfo ['answer_explanation_equation']);
        } else {
            $newarray ['explanation'] = ($questionInfo['answer_explanation']!= '' && $questionInfo['answer_explanation']!= NULL)?htmlentities($questionInfo['answer_explanation']):'';
        }
        
        $newarray['refer_book_name'] = '';
        $newarray['refer_book_chapter'] = '';
        
        /*if(in_array($newarray['category_title'], array('BIBLE New Testament', 'BIBLE Old Testament'))) {*/
        
        /* if(in_array(strtolower($newarray['category_title']), array('bible new testament', 'bible old testament'))) */
        {
            if(!empty($questionInfo['refer_book_name'])) {
                $newarray['refer_book_name'] = $questionInfo['refer_book_name'];
            }
            
            if(!empty($questionInfo['refer_book_chapter'])) {
                $newarray['refer_book_chapter'] = $questionInfo['refer_book_chapter'];
            }            
        }
        
        return $newarray;
    }
    
    
    public function getParentImage($childId){
        $tblParentInfo = new Application_Model_DbTable_ParentInfo ();
        //get parent info
        $parentData = $tblParentInfo->getParentDataUsingChildId($childId);
        if (!empty($parentData['parent_image'])) {
            $parentImage = AWS_S3_URL . 'parent/' . $parentData['parent_image'];
        } else {
            $serverUrl = new Zend_View_Helper_ServerUrl();
            $baseUrl = new Zend_View_Helper_BaseUrl(); 
            $parentImage = $serverUrl->serverUrl().$baseUrl->baseUrl('images/no-image.png');
        }
        return $parentImage;
    }

    public function getSubjectIdAndPoint($qId,$gradId, $childId){
        $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        $childQuestion = new Application_Model_DbTable_ChildQuestion ();
        $subjectInfo = $childQuestion->GetsubjectId($qId);
        $subjectId = $subjectInfo ['subject_id'];
        $cateId = $subjectInfo ['category_id'];
        $ReqInfoforcate = $tblQuestionRequest->getRequestforcategory($subjectId, $gradId, $childId);
        $subpoint = 0;
        foreach ($ReqInfoforcate as $req) {
            if ($req['points_type'] == 'A') {
                $subpoint = $subpoint + $req['points'];
}
            if ($req['points_type'] == 'D') {
                $subpoint = $subpoint - $req['points'];
            }
        }
        $subjectId = !empty($subjectId) ? $subjectId : 0;
        return array("subjectId" => $subjectId, "subjectPoints" => $subpoint);
    }

}
