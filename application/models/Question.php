<?php

class Application_Model_Question extends Zend_Loader_Autoloader {
    /* defined all object variables that are used in entire class
     */

    private $_tblQuestion;
    private $_tblOptions;
    private $_tblChildRequest;
    private $_tblChildQuestion;
    private $_tblGoals;
    private $_tblChallenges;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        require_once APPLICATION_PATH . '/../library/function_image_array.php';
        //creates model class for childquestion
        $this->_tblQuestion = new Application_Model_DbTable_ChildQuestion();
        //creates model class for question option
        $this->_tblOptions = new Application_Model_DbTable_QuestionOptions();
        //creates model class for model file ChildQuestionRequest
        $this->_tblChildRequest = new Application_Model_DbTable_ChildQuestionRequest ();
        //creates model class for model file RequestQuestion
        $this->_tblChildQuestion = new Application_Model_DbTable_RequestQuestion ();
        //creates object of model file ChildGoals
        $this->_tblGoals = new Application_Model_DbTable_ChildGoals ();
        $this->_tblChallenges = new Application_Model_DbTable_ChildChallenges();
    }

    /**
     * @desc Function to get question detail and option
     * @param url_of_question
     * @author suman khatri on 13th November 2013
     * @returns array
     */
    public function getQuestion($qKey) {
        $whereQ = "url_of_question = '$qKey'"; //creates where condtion to fetch question detail
        $question = $this->_tblQuestion->fetchRow($whereQ); //fetches question detail
        $questionId = $question['bal_question_id']; //get question id from question detail
        //creates where condition to fetch question option
        $whereo = "question_id = $questionId and answer ='Y'";
        $options = $this->_tblOptions->fetchRow($whereo); //fetches question option
        //assign question detail an options to a common array
        $data = array('question' => $question, 'option' => $options);
        return $data; //return the commmon array with question detail and option
    }

    /**
     * @desc Function to get asked question to child
     * @param categoryId,firstdate,lastDate,gradeId,childId
     * @author suman khatri on 13th November 2013 modified on 18th November 2013
     * @returns array
     */
    public function getAskedQuestion($categoryId, $startDate, $endDate, $gradeId, $modeOfQ, $childId, $sortCol = null, $sortOr = null, $sWhere = null) {
        if (empty($modeOfQ) && $modeOfQ == null) {//fetches data if $modeOfQ is blank or null
            $questionData = $this->_tblQuestion->getAskedQuestionsOfChildforReport($categoryId, $startDate, $endDate
                , $gradeId, $childId, $sortCol, $sortOr, $sWhere);
        } else {//fetches data if $modeOfQ is not blank or null
            //make $categoryId equal to blank if $categoryId is blank or null
            if (empty($categoryId) && $categoryId == null) {
                $categoryId = '';
            }
            $modeOfQ1 = $modeOfQ;
            if ($modeOfQ == 'notset') {//make $modeOfQ equal to blank if $modeOfQ is equal to notset
                $modeOfQ = '';
            }
            $questionData = $this->_tblQuestion->getAskedQuestionsOfChild($categoryId, $startDate, $endDate, $gradeId
                , $modeOfQ, $childId, $sortCol, $sortOr, $sWhere);
        }
        $questionArray = array();
        $i = 0;
        //formates array according to useer requirement
        foreach ($questionData as $qData) {
            /*
            $questionArray [$i] ['psvCode'] = strtoupper($qData ['category_code']);
            if (!empty($modeOfQ1) && $modeOfQ1 != null) {//fetches data if $modeOfQ is blank or null
                $questionArray [$i] ['psvCode'] .= "<br>(" . $qData ['subtopic_name'] . ")";
            }
            */
            
            if($qData['subject_name'] == 'math.content') {
               $qData['subject_name'] = 'math'; 
            }
            
            $questionArray[$i]['psvCode'] = $qData['subject_name'].', '.$qData['domain_name'];
            if(!empty($qData['subtopic_name'])) {
                $questionArray[$i]['psvCode'] .= ' ('.$qData['subtopic_name'].')';
            }
            
            $questionArray [$i] ['question_id'] = $qData ['question_id'];
            if (!empty($qData ['question_equation_images']) && $qData ['question_equation_images'] != null) {
                $questionArray [$i] ['question'] = nl2br(My_Functions::generateImages($qData['question_equation_images'], $qData['question_display']));
            } else {
                $questionArray [$i] ['question'] = nl2br($qData ['question']);
            }
            if (!empty($qData['option_equation_image_name']) && $qData['option_equation_image_name'] != null) {
                $questionArray[$i]['option'] = My_Functions::generateImages($qData['option_equation_image_name'], $qData['option_equation']);
            } else {
                $questionArray[$i]['option'] = $qData['option'];
            }
            if ($qData ['points_type'] == 'D' || $qData ['points_type'] == '') {
                $sign = "-";
            } else {
                $sign = "+";
            }
            $questionArray [$i] ['points_type'] = $qData ['points_type'];
            if ($qData ['response_points'] > 0) {
                $questionArray [$i] ['pointsScored'] = $sign . $qData ['response_points'];
            } else {
                $questionArray [$i] ['pointsScored'] = 0;
            }
            $questionArray [$i] ['mode'] = $qData ['request_type'];
            $questionArray [$i] ['latitude'] = $qData ['latitude'];
            $questionArray [$i] ['longitude'] = $qData ['longitude'];
            $questionArray [$i] ['device_response_time'] = $qData ['device_response_time'];
            $questionArray [$i] ['request_date'] = $qData ['request_date'];
            $questionArray [$i] ['answered_option_id'] = $qData ['answered_option_id'];
            $questionArray [$i] ['right_option_id'] = $qData ['right_option_id'];
            if (!empty($qData ['right_option_equation_image_name']) && $qData ['right_option_equation_image_name'] != null) {
                $questionArray[$i]['right_option'] = My_Functions::generateImages($qData['right_option_equation_image_name'], $qData['right_option_equation']);
            } else {
                $questionArray [$i] ['right_option'] = $qData ['right_option'];
            }
            $i++;
        }
        return $questionArray; //returns array
    }

    /**
     * @desc Function to get question count for child
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @returns array
     */
    public function getQuestionCountforChild($childId) {
        //getting question count detail
        $questionCountdata = $this->_tblChildRequest->getQuestionforChild($childId);
        return $questionCountdata; //return array with question count detail
    }

    /**
     * @desc Function to add request for challenge
     * @param $childId,$gradeId
     * @author suman khatri on 29th November 2013
     * @returns result
     */
    public function addRequestForChallenge($childId, $gradeId) {
        $dataForQuestionRequest = array(
            'request_type' => 'C',
            'child_id' => $childId,
            'grade_id' => $gradeId,
            'request_date' => date("Y-m-d H:i:s")
        );
        $requestId = $this->_tblChildRequest->insert($dataForQuestionRequest);
        return $requestId;
    }

    /**
     * @desc Function to add data for challenge
     * @param $requestId,$challengeQuestionId,$subjectId,$challengeTime,$challengePoints
     * @author suman khatri on 29th November 2013
     * @returns result
     */
    public function addQuestionDataForChallenge($requestId, $challengeQuestionId, $subjectId, $challengeTime, $challengePoints, $parId,$domainId) {
        //creates model class for model file ChildQuestionRequest
        $tblchildCha = new Application_Model_DbTable_ChildChallenges ();
        $dataChallenge = array(
            'request_id' => $requestId,
            'question_id' => $challengeQuestionId,
            'subject_id' => $subjectId,
            'time_to_answer' => $challengeTime,
            'points' => $challengePoints,
            'parent_id' => $parId,
            'domain_id' =>$domainId,
            'created_date' => date("Y-m-d H:i:s")
        );
        $challengesId = $tblchildCha->insert($dataChallenge);
        return $challengesId;
    }

    /**
     * @desc Function to add request  data for challenge
     * @param $requestId,$challengeQuestionId,$subjectId,$challengeTime,$challengePoints
     * @author suman khatri on 30th November 2013
     * @returns result
     */
    public function addRequestDataForChallenge($challengeQuestionId, $challengesAnswerId, $requestId, $challengePoints) {
        //data to be insert
        $ChildQetionData = array(
            'child_device_id' => 0,
            'question_id' => $challengeQuestionId,
            'answered_option_id' => 0,
            'request_id' => $requestId,
            'points_offered' => $challengePoints,
            'created_date' => date("Y-m-d H:i:s")
        );
        $this->_tblChildQuestion->insert($ChildQetionData); //inserts data into DB
    }

    /**
     * @desc Function to get challenge for child
     * @param $childId
     * @author suman khatri on 30th November 2013
     * @returns array
     */
    public function getChallengeOfChild($childId, $parentId) {
        //get challenges of child
        $child_challenge = $this->_tblChildRequest->getChildChallenges($childId);
        if (empty($child_challenge)) {
            return FALSE;
        }

        if (!empty($child_challenge['request_date']) && empty($child_challenge['points_type'])) {
            $todayDateDiff = time() - strtotime($child_challenge['request_date']);
            if ($todayDateDiff >= 24 * 60 * 60) {
                $this->cancelChallengeOfChild($child_challenge['request_id']);
                return $this->getChallengeOfChild($childId, $parentId);
            }
        }

        if (!empty($child_challenge['device_response_time'])) {
            $timeR = $child_challenge['device_response_time'];
            if ($timeR > 60) {
                $responseTime = round($timeR / 60) . " Min.";
            } else {
                $responseTime = $timeR . " Sec.";
            }
            $child_challenge['request_date_ago'] = $responseTime;
        }

        if (isset($child_challenge['answered_option_id']) && !empty($child_challenge['answered_option_id'])) {

            $opTions = $this->getKidResponseOptionOfquestion($child_challenge['answered_option_id']);
            $child_challenge['kid_response_option'] = $opTions['option'];
            if (!empty($opTions ['option_equation_image_name'])) {
                $child_challenge['kid_response_option_image'] = $opTions['option_equation_image_name'];
                $child_challenge['kid_response_option_equation'] = $opTions['option_equation'];
            }

            $child_challenge['answer_type'] = ($child_challenge['question_option_id'] == $child_challenge['answered_option_id']);
        }

        return $child_challenge; //return array
    }

    /**
     * @desc Function to cancel  challenge for child
     * @param requestId
     * @author suman khatri on 30th November 2013
     * @returns array
     */
    public function cancelChallengeOfChild($requestId) {
        $result = $this->_tblChildRequest->RemoveRequest($requestId);
        $result = $this->_tblChildQuestion->removeQuestionResponce($requestId);
        $result = $this->_tblChallenges->removeChalangesData($requestId);
        return $result;
    }

    /**
     * @desc Function to get challenge for child of this week
     * @param $childId
     * @author suman khatri on 30th November 2013
     * @returns count
     */
    public function getChallengeQuestionOfThisWeek($childId) {
        //get challenges of child of this week
        $childChallenges = $this->_tblChildRequest->getChildChallengesThisWeek($childId);
        return $childChallenges; //return array
    }

    /**
     * @desc Function to get options od question
     * @param $questionId
     * @author suman khatri on 30th November 2013
     * @returns array
     */
    public function getRightOptionOfquestion($questionId) {
        //creates where condition to fetch question option
        $whereo = "question_id = $questionId and answer ='Y'";
        $options = $this->_tblOptions->fetchRow($whereo); //fetches question option
        return $options; //return the commmon array with question detail and option
    }

    /**
     * @desc Function to get options od question
     * @param $questionId
     * @author suman khatri on 30th November 2013
     * @returns array
     */
    public function getKidResponseOptionOfquestion($optionId) {
        //creates where condition to fetch question option
        $whereo = "question_option_id = $optionId";
        $options = $this->_tblOptions->fetchRow($whereo); //fetches question option
        return $options; //return the commmon array with question detail and option
    }

    /**
     * @desc Function to get scorecardStats
     * @param $childId,$grade
     * @author suman khatri on 3rd December 2013
     * @returns array
     */
    public function getScoreCardStats($childId, $grade) {
        $statsData = $this->_tblChildRequest->getScoreCardStats($childId, $grade); //fetches scorecard stats
        return $statsData; //return the commmon array with question detail and option
    }

    /**
     * @desc Function to get mobileStats
     * @param $childId,$grade
     * @author suman khatri on 3rd December 2013
     * @returns array
     */
    public function getMobileStats($childId, $grade) {
        $statsQuestionData = $this->_tblChildRequest->getStatsOfQuestionForMobile($childId, $grade); //fetches moblie question  stats
        $statsChallengeData = $this->_tblChildRequest->getStatsOfChallengesForMobile($childId, $grade); //fetches mobile challenges stats
        $childGoal = $this->_tblGoals->fetchGoals($childId);
        //formates array according to requirement
        $resultArray = array(
            'monthQuestionData' => $statsQuestionData['monthData'],
            'thisWeekQuestionData' => $statsQuestionData['thisWeekData'],
            'todayQuestionData' => $statsQuestionData['todayData'],
            'totalQuestionData' => $statsQuestionData['totalData'],
            'monthChallengeData' => $statsChallengeData['monthChallengeData'],
            'thisWeekChallengeData' => $statsChallengeData['thisWeekChallengeData'],
            'todayChallengeData' => $statsChallengeData['todayChallengeData'],
            'totalPoints' => $statsQuestionData['totalPoints'],
            'exactGoal' => $childGoal['weekly_points']
        );
        return $resultArray; //return the commmon array with question detail and option
    }

    /**
     * @desc Function to get mobileStats
     * @param $childId,$grade
     * @author suman khatri on 3rd December 2013
     * @returns array
     */
    public function getMobileStatsForLite($childId, $grade) {
        $statsQuestionData = $this->_tblChildRequest->getStatsOfQuestionForFinnyLite($childId, $grade); //fetches moblie question  stats
        $resultArray = array(
            'totalQuestionData' => $statsQuestionData['totalData'],
            'totalPoints' => $statsQuestionData['totalPoints'],
        );
        return $resultArray; //return the commmon array with question detail and option
    }

    public function formateMobileStatsDataLite($statsData) {
        $totalQuestion = $statsData['totalQuestionData']['TotalCorrect'] + $statsData['totalQuestionData']['TotalWrong'] +
            $statsData['totalQuestionData']['UnAnswered'];
        $totalAnswered = $statsData['totalQuestionData']['TotalCorrect'] + $statsData['totalQuestionData']['TotalWrong'];
        $totalRanswer = $statsData['totalQuestionData']['TotalCorrect'] ? $statsData['totalQuestionData']['TotalCorrect'] : 0;
        $totalAnswered = $totalAnswered ? $totalAnswered : 0;
        $totalQuestion = $totalQuestion ? $totalQuestion : 0;
        $resultArray = array(
            'totalQuestion' => $totalQuestion,
            'totalRanswer' => $totalRanswer,
            'totalAnswered' => $totalAnswered,
            'totalPoints' => $statsData['totalPoints']
        );
        return $resultArray;
    }

    /**
     * @desc Function to formate mobileStats
     * @param $statsData
     * @author suman khatri on 3rd December 2013
     * @returns array
     */
    public function formateMobileStatsData($statsData) {
        $countRtoday = $statsData['todayQuestionData']['TotalCorrect'] ? $statsData['todayQuestionData']['TotalCorrect'] : 0;
        $countRweek = $statsData['thisWeekQuestionData']['TotalCorrect'] ? $statsData['thisWeekQuestionData']['TotalCorrect'] : 0;
        $countRmonth = $statsData['monthQuestionData']['TotalCorrect'] ? $statsData['monthQuestionData']['TotalCorrect'] : 0;
        $countQtoday = $statsData['todayQuestionData']['TotalCorrect'] + $statsData['todayQuestionData']['TotalWrong'] +
            $statsData['todayQuestionData']['UnAnswered'];
        $countQtoday = $countQtoday ? $countQtoday : 0;
        $countQweek = $statsData['thisWeekQuestionData']['TotalCorrect'] + $statsData['thisWeekQuestionData']['TotalWrong'] +
            $statsData['thisWeekQuestionData']['UnAnswered'];
        $countQweek = $countQweek ? $countQweek : 0;
        $countQmonth = $statsData['monthQuestionData']['TotalCorrect'] + $statsData['monthQuestionData']['TotalWrong'] +
            $statsData['monthQuestionData']['UnAnswered'];
        $countQmonth = $countQmonth ? $countQmonth : 0;
        $countAtoday = $statsData['todayQuestionData']['TotalCorrect'] + $statsData['todayQuestionData']['TotalWrong'] - $statsData['todayQuestionData']['UnAnsweredChallenge'];
        $countAtoday = $countAtoday ? $countAtoday : 0;
        $countAweek = $statsData['thisWeekQuestionData']['TotalCorrect'] + $statsData['thisWeekQuestionData']['TotalWrong'] - $statsData['thisWeekQuestionData']['UnAnsweredChallenge'];
        $countAweek = $countAweek ? $countAweek : 0;
        $countAmonth = $statsData['monthQuestionData']['TotalCorrect'] + $statsData['monthQuestionData']['TotalWrong'] - $statsData['monthQuestionData']['UnAnsweredChallenge'];
        $countAmonth = $countAmonth ? $countAmonth : 0;
        $totalTodayPoint = $statsData['todayQuestionData']['CorrectAnswerPoints'] - $statsData['todayQuestionData']['WrongAnswerPoints'];
        if ($totalTodayPoint < 0) {
            $totalTodayPoint = 0;
        } else {
            $totalTodayPoint = $totalTodayPoint;
        }
        $totalWeekPoint = $statsData['thisWeekQuestionData']['CorrectAnswerPoints'] - $statsData['thisWeekQuestionData']['WrongAnswerPoints'];
        if ($totalWeekPoint < 0) {
            $totalWeekPoint = 0;
        } else {
            $totalWeekPoint = $totalWeekPoint;
        }
        $totalMonthPoint = $statsData['monthQuestionData']['CorrectAnswerPoints'] - $statsData['monthQuestionData']['WrongAnswerPoints'];
        if ($totalMonthPoint < 0) {
            $totalMonthPoint = 0;
        } else {
            $totalMonthPoint = $totalMonthPoint;
        }
        $chalTodayPoint = $statsData['todayChallengeData']['CorrectAnswerPoints'] - $statsData['todayChallengeData']['WrongAnswerPoints'];
        if ($chalTodayPoint < 0) {
            $chalTodayPoint = 0;
        } else {
            $chalTodayPoint = $chalTodayPoint;
        }
        $chalWeekPoint = $statsData['thisWeekChallengeData']['CorrectAnswerPoints'] - $statsData['thisWeekChallengeData']['WrongAnswerPoints'];
        if ($chalWeekPoint < 0) {
            $chalWeekPoint = 0;
        } else {
            $chalWeekPoint = $chalWeekPoint;
        }
        $chalMonthPoint = $statsData['monthChallengeData']['CorrectAnswerPoints'] - $statsData['monthChallengeData']['WrongAnswerPoints'];
        if ($chalMonthPoint < 0) {
            $chalMonthPoint = 0;
        } else {
            $chalMonthPoint = $chalMonthPoint;
        }
        $totalQuestion = $statsData['totalQuestionData']['TotalCorrect'] + $statsData['totalQuestionData']['TotalWrong'] +
            $statsData['totalQuestionData']['UnAnswered'];
        $totalAnswered = $statsData['totalQuestionData']['TotalCorrect'] + $statsData['totalQuestionData']['TotalWrong'];
        $totalRanswer = $statsData['totalQuestionData']['TotalCorrect'];
        $resultArray = array(
            'countRtoday' => $countRtoday,
            'countRweek' => $countRweek,
            'countRmonth' => $countRmonth,
            'countQtoday' => $countQtoday,
            'countQweek' => $countQweek,
            'countQmonth' => $countQmonth,
            'countAtoday' => $countAtoday,
            'countAweek' => $countAweek,
            'countAmonth' => $countAmonth,
            'pointQtoday' => $totalTodayPoint,
            'pointQweek' => $totalWeekPoint,
            'pointQmonth' => $totalMonthPoint,
            'pointCtoday' => $chalTodayPoint,
            'pointCweek' => $chalWeekPoint,
            'pointCmonth' => $chalMonthPoint,
            'totalQuestion' => $totalQuestion,
            'totalRanswer' => $totalRanswer,
            'totalAnswered' => $totalAnswered,
            'totalPoints' => $statsData['totalPoints'],
            'exactGoal' => $statsData['exactGoal']
        );
        return $resultArray;
    }

    /**
     * @desc Function to formate data for scorecard pages
     * @param $questionArray
     * @author suman khatri on 8th December 2013
     * @returns $questionArray
     */
    /*public function formateDataForScorecard($arrayData) {
        if (!empty($arrayData['QuestionData'] ['question_id'])) {
            $qId = $arrayData ['QuestionData'] ['question_id'];
            //creates where condition to fetch question option
            $whereo = "question_id = $qId and answer ='Y'";
            $opTions = $this->_tblOptions->fetchRow($whereo); //fetches question option
            if ($opTions ['question_option_id'] != $arrayData ['QuestionData'] ['answered_option_id']) {
                $arrayData['QuestionData'] ['right_option'] = $opTions ['option'];
                $arrayData['QuestionData'] ['right_option_id'] = $opTions ['question_option_id'];
                $arrayData['QuestionData'] ['right_option_equation'] = $opTions ['option_equation'];
                if (!empty($opTions ['option_equation_image_name'])) {
                    $arrayData ['QuestionData'] ['right_option_image'] = $opTions ['option_equation_image_name'];
                }
            }
        }
        return $arrayData;
    }
    */
    
    public function getRightOption($questionId) {
        $whereCond = "question_id = $questionId and answer ='Y'";
        return $this->_tblOptions->fetchRow($whereCond);
    }

    /**
     * @desc Function to formate data for report pages
     * @param $questionArray
     * @author suman khatri on 10th December 2013
     * @returns $questionArray
     */
    public function getRightResponceForQuestion($questionArray) {
        for ($i = 0; $i < count($questionArray); $i ++) {
            if (!empty($questionArray [$i] ['question_id'])) {
                $qId = $questionArray [$i] ['question_id'];
                $whereo = "question_id = $qId and answer ='Y'";
                $opTions = $this->_tblOptions->fetchRow($whereo); //fetches question option
                if (!empty($opTions ['option_equation_image_name']) && $opTions ['option_equation_image_name'] != null) {
                    $questionArray[$i]['right_option'] = My_Functions::generateImages($opTions['option_equation_image_name'], $opTions['option_equation']);
                } else {
                    $questionArray [$i] ['right_option'] = $opTions ['option'];
                }
            }
            $questionArray [$i] ['right_option_id'] = $opTions ['question_option_id'];
        }
        return $questionArray;
    }

    /*     * **********function for fetching row of data ***************** */

    public function checkKidResposeAllready($requestId) {
        $where = "request_id = '$requestId'";
        return $this->_tblChildQuestion->fetchRow($where);
    }

    /*     * **********function for fetching row of data ***************** */

    public function getTotalQuestions() {

        $data = $this->_tblQuestion->getTotalQuestions();
        $totalQuestions = (int) $data['totalQuestions'];
        $totalDraftQuestions = (int) $data['totalDraftQuestions'];
        $totalExpiredQuestions = (int) $data['totalExpiredQuestions'];
        if ((!empty($totalQuestions) && $totalQuestions > 0) || (!empty($totalDraftQuestions) && $totalDraftQuestions > 0)
                || (!empty($totalExpiredQuestions) && $totalExpiredQuestions > 0)){
            $cols = array(
                'cols' => array(
                    array('id' => '', 'label' => 'Questions', 'pattern' => '', 'type' => 'string'),
                    array('id' => '', 'label' => 'count', 'pattern' => '', 'type' => 'number')
                ),
                'rows' => array(
                    array('c' => array(
                            array('v' => 'Questions', 'f' => null),
                            array('v' => $totalQuestions, 'f' => null)
                        )),
                    array('c' => array(
                            array('v' => 'Expired Questions', 'f' => null),
                            array('v' => $totalExpiredQuestions, 'f' => null)
                        )),
                    array('c' => array(
                            array('v' => 'Draft Questions', 'f' => null),
                            array('v' => $totalDraftQuestions, 'f' => null)
                        )),
                )
            );
            return json_encode($cols);
        }else{
            return false;
        }
    }

}
