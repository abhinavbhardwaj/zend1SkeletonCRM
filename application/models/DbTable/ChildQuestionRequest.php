<?php

/*
 * This is a model class for Child Information
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */

class Application_Model_DbTable_ChildQuestionRequest extends Zend_Db_Table_Abstract
{

    // This is name of Table
    protected $_name = 'bal_child_question_requests';

    /**
     * function to add request 
     * @param array(data)
     * @return Last insert Id
     */
    public function AddRequest($data)
    {
        return $this->insert($data);
    }

    /**
     * function to get request of today for child
     * @param int childId
     * @return Array
     */
    public function getRequestIdforToday($childId)
    {
        //echo $date_before1 = date("Y-m-d H:i:s", strtotime(date("Y-m-d") . " -1 days"));
        $date = date("Y-m-d 0:0:0");
        $where = "child_id = $childId and request_date > '$date' and (response_date is not NULL or request_type = 'C')";
        $res = $this->fetchAll($where);
        return $res;
    }

    /**
     * function to get request of this week for child
     * @param int childId
     * @return Array
     */
    public function getRequestIdforThisweek($childId)
    {
        $first_day_of_week = date('Y-m-d H:i:s', strtotime('Last Monday', time()));
        $date = date('Y-m-d H:i:s');
        $where = "child_id = $childId and request_date > '$first_day_of_week' and (response_date is not NULL or request_type = 'C')";
        $order = "request_id DESC";
        $res = $this->fetchAll($where, $order);
        return $res;
    }

    /**
     * function to get all request of this month for child
     * @param int childId
     * @return Array
     */
    public function getRequestIdforThisMonth($childId)
    {
        $first_day_of_month = date('Y-m-1 0:0:0');
        $date = date('Y-m-d H:i:s');
        $where = "child_id = $childId and request_date >= '$first_day_of_month' and (response_date is not NULL or request_type = 'C')";
        $order = "request_id DESC";
        $res = $this->fetchAll($where, $order);
        return $res;
    }

    /**
     * function used to check existance of reference id in the DB
     * @param int referenceId
     * @return Array
     */
    public function isExistReferenceId($refId)
    {
        $where = "request_id = $refId";
        $refIdExist = $this->fetchRow($where);
        if ($refIdExist) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function to get list of already asked question for a child
     * @param int childId
     * @return Array
     */
    public function getAskedQuestion($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('blqr' => 'bal_child_question_requests'), array('blqr.request_id'));
        $select->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'));
        $select->where("blqr.child_id = ?", $childId);
        $select->group("bcq.question_id");
        $questionid = $db->fetchAll($select);
        return $questionid;
    }

    /**
     * function to remove request
     * @param int requestId
     * @return Array
     */
    public function RemoveRequest($rId)
    {
        $where = "request_id = $rId";
        return $this->delete($where);
    }

    /**
     * function used to get request of particular child with grade
     * @param int gradeId,childId
     * @return Array
     */
    public function getRequestIdforTodaywithgrade($childId, $grade)
    {
        $date = date("Y-m-d 0:0:0");
        $where = "child_id = $childId and request_date >= '$date' and grade_id = $grade and (response_date is not NULL or request_type = 'C')";
        $res = $this->fetchAll($where);
        return $res;
    }

    /**
     * function to get request of this week of particular child with grade
     * @param int gradeId,childId
     * @return Array
     */
    public function getRequestIdforThisweekwithgrade($childId, $grade)
    {
        if (date('w') == 0) {
            $first_day_of_week = date('Y-m-d 0:0:0');
        } else {
            $first_day_of_week = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
        }

        $last_day_of_week = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($first_day_of_week)));
        $date = date('Y-m-d H:i:s');
        $where = "child_id = $childId and request_date >= '$first_day_of_week' and request_date <='$last_day_of_week' and grade_id = $grade and (response_date is not NULL or request_type = 'C')";
        $res = $this->fetchAll($where);
        return $res;
    }

    /**
     * function to get request of this month with grade for a child 
     * @param int gradeId,childId
     * @return Array
     */
    public function getRequestIdforThisMonthwithgrade($childId, $grade)
    {
        $first_day_of_month = date('Y-m-1 0:0:0');
        $date = date('Y-m-d H:i:s');
        $where = "child_id = $childId and request_date >= '$first_day_of_month' and grade_id = $grade and (response_date is not NULL or request_type = 'C')";
        $order = "request_id DESC";
        $res = $this->fetchAll($where);
        return $res;
    }

    /**
     * function to get all requests with questions having particular category for a child 
     * @param int categoryId,gradeId,childId
     * @return Array
     */
    public function getRequestforcategory($subjectId, $gradeId, $childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))
                ->joinInner(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id 	', array('bcq.child_device_id'))
                ->joinLeft(array('bq' => 'bal_questions'), 'bq.bal_question_id = bcq.question_id', array('bq.category_id'))
                ->joinLeft(array('blc' => 'bal_question_categories'), 'blc.category_id = bq.category_id', array('bq.category_id'))
                ->where("blc.subject_id = $subjectId")
                ->where("blqr.child_id = $childId")
                ->where("blqr.grade_id = $gradeId");
        $questionid = $db->fetchAll($select);
        return $questionid;
    }

    /*     * function to get total points achived by child
     * in current weeek
     * @param int child id
     * @retrun array 
     */

    public function getTotalAchivePoints($childId, $weeklyGoalDateRange)
    {

        if (!empty($weeklyGoalDateRange)) {
            $startDate = date('Y-m-d 00:00:00', strtotime($weeklyGoalDateRange[0]['start_date']));
            $endDate = date('Y-m-d 23:59:59', strtotime($weeklyGoalDateRange[0]['end_date']));

            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('totalA' => $this->_name), array("SUM(if(totalA.points_type = 'A',totalA.points,0)) AS totalPointsA",
                        "SUM(if((totalA.points_type = 'D'OR
(totalA.request_type = 'W' AND totalA.points IS NOT NULL AND totalA.points_type IS NULL)),totalA.points,0)) AS totalPointsD"))
                    ->where("totalA.child_id = $childId")
                    ->where("totalA.request_date >= '$startDate'")
                    ->where("totalA.request_date <= '$endDate'");
            $totalPoints = $db->fetchAll($select);
            return $totalPoints;
        }
    }

    /**
     * function to get how many times a question asked  
     * @param int questionId
     * @return count(integer value)
     * created by suman on 16 september 2013
     */
    public function getquestionAskedTime($qId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("bcq.question_id = $qId and blqr.response_date IS NOT NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function to get how mant times right answer given for the question  
     * @param int questionId
     * @return count(integer value)
     * created by suman on 16 september 2013
     */
    public function getquestionRightAnswerTime($qId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("bcq.question_id = $qId")
                ->where("blqr.points_type = 'A' and blqr.response_date IS NOT NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function to get how many times wrong answer given for the question 
     * @param int questionId
     * @return count(integer value)
     * created by suman on 16 september 2013
     */
    public function getquestionWrongAnswerTime($qId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("bcq.question_id = $qId")
                ->where("blqr.points_type = 'D' and blqr.response_date IS NOT NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function to get how many times question was unanswered
     * @param int questionId
     * @return count(integer value)
     * created by suman on 16 september 2013
     */
    public function getquestionUnAnswerTime($qId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.request_id'))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("bcq.question_id = '$qId' and blqr.response_date IS NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function for fetch week data for start and end date defined
     * @param int childId ,gradeId,start date amd end date
     * @return Array
     * created by suman on 23 september 2013
     */
    public function getRequestIdforLastWeekwithgrade($childId, $gradeId, $start_date, $end_date)
    {
        $where = "child_id = $childId and request_date >= '$start_date' and request_date <='$end_date' and grade_id = $gradeId";
        $res = $this->fetchAll($where);
        return $res;
    }

    /**
     * function for get responce time when question answered with correct answer
     * @param int questionId
     * @return Array
     * created by suman on 27 september 2013
     */
    public function getCorrectResponceTime($qId)
    {
        $responceTime = 0;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array("SUM(if(blqr.points_type = 'A',blqr.device_response_time,0)) AS correctResponseTime"))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.request_id'))
                ->where("bcq.question_id = '$qId'");
        $questions = $db->fetchAll($select);
        foreach ($questions as $q) {
            $responceTime = $responceTime + $q['correctResponseTime'];
        }
        return $responceTime;
    }

    /**
     * function for get responce time when question answered with correct answer
     * @param int questionId
     * @return Array
     * created by suman on 27 september 2013
     */
    public function getWrongResponceTime($qId)
    {
        $responceTime = 0;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array("SUM(if(blqr.points_type = 'D',blqr.device_response_time,0)) AS wrongResponseTime"))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.request_id'))
                ->where("bcq.question_id = '$qId'");
        $questions = $db->fetchAll($select);
        foreach ($questions as $q) {
            $responceTime = $responceTime + $q['wrongResponseTime'];
        }
        return $responceTime;
    }

    /**
     * get current challenge details
     * @param type $childId
     * @return Zend_Db_Table_Row
     */
    public function getChildChallenges($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();

        $select->from(array('blqr' => 'bal_child_question_requests'), array(
            "blqr.request_date", "blqr.points_type", "blqr.response_date", "blqr.device_response_time",
            "blqr.longitude", "blqr.latitude", "blqr.request_id", "blqr.grade_id"
        ));

        $select->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array("bcq.*"));
        $select->joinLeft(array('blq' => 'bal_questions'), 'bcq.question_id = blq.bal_question_id', array(
            'blq.question', 'blq.question_equation_image_name', 'medium' => 'blq.difficulty_level',
            'blq.question_equation_images', 'blq.question_display'
        ));

        $select->joinLeft(array('blqo' => 'bal_question_options'), "blqo.question_id = bcq.question_id", array(
            'blqo.option', 'blqo.option_equation_image_name', 'blqo.option_equation', 'blqo.question_option_id'
        ));

        $select->joinLeft(array('bch' => 'bal_challenges'), "bch.request_id = blqr.request_id", array('bch.subject_id', 'bch.domain_id'));
        $select->joinLeft(array('bls' => 'bal_subjects'), "bch.subject_id = bls.subject_id", array('bls.subject_name'));

        $select->joinLeft(array('bqc' => 'bal_question_categories'), "bqc.category_id = blq.category_id", '');
        $select->joinLeft(array('bldo' => 'bal_question_domains'), "bldo.domain_id = bqc.domain_id", array('domain_name' => 'bldo.name'));

        $select->where("blqr.child_id = $childId");
        $select->where("blqr.request_type = 'C'");
        $select->where("blqo.answer = 'Y'");
        $select->order('blqr.request_date DESC');

        $result = $db->fetchRow($select);
        return $result;
    }

    public function getChildChallengesSet($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => $this->_name), array('request_date' => 'blqr.request_date'))
                ->where("blqr.child_id = $childId")
                ->where("blqr.request_type = 'C'")
                ->where("bal_child_questions.child_device_id ='0'")
                ->order('blqr.request_date DESC')
                ->join('bal_child_questions', 'blqr.request_id = bal_child_questions.request_id', array('child_device_id' => 'bal_child_questions.child_device_id'));
        $result = $db->fetchRow($select);
        return $result;
    }

    /**
     * function for get gradelist of child in which questions are asked
     * @param int childId
     * @return Array List
     * created by suman on 28 september 2013
     */
    public function GetGradeofChild($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.request_id'))
                ->joinLeft(array('blg' => 'bal_grades'), 'blqr.grade_id = blg.grades_id', array('blg.grades_id', 'blg.grade_name'))
                ->where("blqr.child_id = '$childId'")
                ->group('blg.grades_id');
        $gradeData = $db->fetchAll($select);
        return $gradeData;
    }

    /**
     * function to get how many times right answers given
     * @param int childId
     * @return count(integer value)
     * created by suman on 30 september 2013
     */
    public function getQuestionRightAnswerTimeforChild($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("blqr.child_id = $childId")
                ->where("blqr.points_type = 'A' and blqr.response_date IS NOT NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function to get how many times wrong answers given
     * @param int childId
     * @return count(integer value)
     * created by suman on 30 september 2013
     */
    public function getQuestionWrongAnswerTimeforChild($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.*'))
                ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id'))
                ->where("blqr.child_id = $childId")
                ->where("blqr.points_type = 'D' and blqr.response_date IS NOT NULL");
        $questions = $db->fetchAll($select);
        return count($questions);
    }

    /**
     * function to get how many questions are attempted
     * @param int childId
     * @return count(integer value)
     * created by suman on 30 september 2013
     */
    public function getQuestionforChild($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array("COUNT(blqr.request_id) as TotalCount",
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong"))
                ->where("blqr.child_id = $childId")
                ->where("blqr.response_date IS NOT NULL or blqr.request_type = 'C'");
        $questions = $db->fetchRow($select);
        return $questions;
    }

    /**
     * @desc function to check request is updated or not
     * @param int requestId
     * @return true or false
     * @author suman on 11 october 2013
     */
    public function CheckRequestUpadteOrNot($rId)
    {
        $where = "(response_date IS NOT NULL or response_date != '') and request_id = $rId";
        $res = $this->fetchRow($where);
        if (count($res) > 0 && !empty($res)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @descfunction for retrive chalanges of child of this  week
     * @param int child id
     * @author suman on 30 November 2013
     * @return count	
     */
    public function getChildChallengesThisWeek($childId)
    {

        if (date('w') == 0) {
            $first_day_of_week = date('Y-m-d 0:0:0');
        } else {
            $first_day_of_week = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
        }
        $last_day_of_week = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($first_day_of_week)));
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => $this->_name), array('points_type' => 'blqr.points_type', 'request_date' => 'blqr.request_date', 'blqr.response_date' => 'blqr.response_date', 'device_response_time' => 'blqr.device_response_time'))
                ->where("blqr.child_id = $childId")
                ->where("blqr.request_type = 'C'")
                ->where("request_date >= '$first_day_of_week' and request_date <='$last_day_of_week'")
                //->where("bal_question_options.answer = 'Y'")
                ->order('blqr.request_date DESC');
        $result = $db->fetchAll($select);
        return count($result);
    }

    /**
     * function used to get request of particular child with grade
     * @param int gradeId,childId
     * @return Array
     */
    public function getScoreCardStats($childId, $grade)
    {
        //condition and variables to get report of current week
        if (date('w') == 0) {
            //$firstDayOfWeek = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime(date("Y-m-d H:i:s")));
        } else {
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
        }
        $lastDayOfWeek = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($firstDayOfWeek)));
        $where1 = "blqr.child_id = $childId and blqr.request_date >= '$firstDayOfWeek' and blqr.request_date <='$lastDayOfWeek' 
				and blqr.grade_id = $grade"; // and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $select1 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnsweredChallenge",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where1);
        $resWeek = $db->fetchRow($select1);
        //condition and variables to get report of today
        //$startDateOfTheDay = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
        $startDateOfTheDay = date('Y-m-d 0:0:0', strtotime(date("Y-m-d H:i:s")));
        //$endDateOfTheDay = date('Y-m-d 23:59:59', strtotime(todayZendDate()));
        $endDateOfTheDay = date('Y-m-d 23:59:59', strtotime(date("Y-m-d H:i:s")));
        $where2 = "blqr.child_id = $childId and blqr.request_date >= '$startDateOfTheDay' and blqr.request_date <= 
					'$endDateOfTheDay' and blqr.grade_id = $grade"; // and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $select2 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnsweredChallenge",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where2);
        $resToday = $db->fetchRow($select2);
        //get last responded question 
        $select3 = $db->select()->from(array(
                            'blqr' => 'bal_child_question_requests'
                                ), array(
                            "blqr.request_id", "blqr.longitude", "blqr.latitude"
                        ))->joinLeft(array(
                            'bcq' => 'bal_child_questions'
                                ), 'blqr.request_id = bcq.request_id', array(
                            "bcq.*"
                        ))->joinLeft(array(
                            'blq' => 'bal_questions'
                                ), 'bcq.question_id = blq.bal_question_id', array(
                            'blq.question', 'blq.question_equation_image_name', 'medium' => 'blq.difficulty_level', 'blq.question_display', 'blq.question_equation_images'
                        ))->joinLeft(array(
                            'blqo' => 'bal_question_options'
                                ), "blqo.question_option_id = bcq.answered_option_id", array(
                            'blqo.option', 'blqo.option_equation_image_name', 'blqo.option_equation'
                        ))->where("blqr.child_id = $childId and blqr.grade_id = $grade")
                        ->where("blqr.response_date != ''")
                        ->order('blqr.request_date DESC')->limit(1);
        $result = $db->fetchRow($select3);

        //get total points of child
        $select4 = $db->select()->from(array(
                            'bcgp' => 'bal_child_grade_points'
                                ), array(
                            "bcgp.*"
                        ))->where("bcgp.child_id = $childId and bcgp.grade_id = $grade")
                        ->order('bcgp.id DESC')->limit(1);
        $resultPoints = $db->fetchRow($select4);
        $arrayData = array('thisWeekData' => $resWeek, 'todayData' => $resToday, 'QuestionData' => $result, 'totalPoints' => $resultPoints['points']);
        return $arrayData;

        /*  formatting of data
         * 
          if (!empty($arrayData['QuestionData'] ['question_id'])) {
          $qId = $arrayData ['QuestionData'] ['question_id'];
          //creates where condition to fetch question option
          $whereo = "question_id = $qId and answer ='Y'";

          $dbTest = new Application_Model_DbTable_QuestionOptions();
          $opTions = $dbTest->fetchRow($whereo); //fetches question option

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
         */
    }

    /**
     * function used to get request of particular child with grade
     * @param int gradeId,childId
     * @author Suman Khatri on 5th Dec
     * @return Array
     */
    public function getStatsOfQuestionForMobile($childId, $grade)
    {
        //condition and variables to get report of current week
        if (date('w') == 0) {
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
        } else {
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
        }
        $lastDayOfWeek = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($firstDayOfWeek)));
        $where1 = "blqr.child_id = $childId and blqr.request_date >= '$firstDayOfWeek' and blqr.request_date <='$lastDayOfWeek' 
				and blqr.grade_id = $grade"; // and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $select1 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnsweredChallenge",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D' OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where1);
        $resWeek = $db->fetchRow($select1);
        //condition and variables to get report of today
        $startDateOfTheDay = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
        $endDateOfTheDay = date('Y-m-d 23:59:59', strtotime(todayZendDate()));
        $where2 = "blqr.child_id = $childId and blqr.request_date >= '$startDateOfTheDay' and blqr.request_date <= 
					'$endDateOfTheDay' and blqr.grade_id = $grade "; //and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $select2 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnsweredChallenge",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where2);
        $resToday = $db->fetchRow($select2);
        //condition and variables to get report of month
        $startDateOfMonth = date('Y-m-1 0:0:0', strtotime(todayZendDate()));
        $endDateOfMonth = date('Y-m-t 23:59:59', strtotime($startDateOfTheDay));
        $where3 = "blqr.child_id = $childId and blqr.request_date >= '$startDateOfMonth' and blqr.request_date <= 
					'$endDateOfMonth' and blqr.grade_id = $grade "; //and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $select3 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnsweredChallenge",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where3);
        $resMonth = $db->fetchRow($select3);
        //condition and variables to get whole report 
        $where4 = "blqr.child_id = $childId and blqr.grade_id = $grade"; //and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $select4 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                ))->where($where4);
        $resTotal = $db->fetchRow($select4);

        //get total points of child
        $select5 = $db->select()->from(array(
                            'bcgp' => 'bal_child_grade_points'
                                ), array(
                            "bcgp.*"
                        ))->where("bcgp.child_id = $childId and bcgp.grade_id = $grade")
                        ->order('bcgp.id DESC')->limit(1);
        $resultPoints = $db->fetchRow($select5);
        $resultArray = array(
            'thisWeekData' => $resWeek,
            'todayData' => $resToday,
            'monthData' => $resMonth,
            'totalData' => $resTotal,
            'totalPoints' => $resultPoints['points']
        );
        return $resultArray;
    }

    /*     * ***********************function for my stats of fiiny lite************** */

    public function getStatsOfQuestionForFinnyLite($childId, $grade)
    {
        //condition and variables to get whole report
        $db = Zend_Db_Table::getDefaultAdapter();
        $where4 = "blqr.child_id = $childId and blqr.grade_id = $grade and (blqr.response_date is not NULL or blqr.request_type = 'C')";
        $select4 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.response_date IS NULL,1,0)) as UnAnswered",
                ))->where($where4);
        $resTotal = $db->fetchRow($select4);
        //get total points of child
        $select5 = $db->select()->from(array(
                            'bcgp' => 'bal_child_grade_points'
                                ), array(
                            "bcgp.*"
                        ))->where("bcgp.child_id = $childId and bcgp.grade_id = $grade")
                        ->order('bcgp.id DESC')->limit(1);
        $resultPoints = $db->fetchRow($select5);
        $resultArray = array(
            'totalData' => $resTotal,
            'totalPoints' => $resultPoints['points']
        );
        return $resultArray;
    }

    /**
     * function used to get request of particular child with grade
     * @param int gradeId,childId
     * @author Suman Khatri on 5th Dec
     * @return Array
     */
    public function getStatsOfChallengesForMobile($childId, $grade)
    {
        //condition and variables to get report of current week challenges
        if (date('w') == 0) {
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
        } else {
            $firstDayOfWeek = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
        }
        $lastDayOfWeek = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($firstDayOfWeek)));
        $where1 = "blqr.child_id = $childId and blqr.request_date >= '$firstDayOfWeek' and blqr.request_date <='$lastDayOfWeek' 
				and blqr.grade_id = $grade and blqr.request_type = 'W'";
        $db = Zend_Db_Table::getDefaultAdapter();
        $select1 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where1);
        $resWeek = $db->fetchRow($select1);
        //condition and variables to get report of today challenges
        $startDateOfTheDay = date('Y-m-d 0:0:0', strtotime(todayZendDate()));
        $endDateOfTheDay = date('Y-m-d 23:59:59', strtotime(todayZendDate()));
        $where2 = "blqr.child_id = $childId and blqr.request_date >= '$startDateOfTheDay' and blqr.request_date <= 
					'$endDateOfTheDay' and blqr.grade_id = $grade and blqr.request_type = 'W'";
        $select2 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where2);
        $resToday = $db->fetchRow($select2);
        //condition and variables to get report of month challenges
        $startDateOfMonth = date('Y-m-1 0:0:0', strtotime(todayZendDate()));
        $endDateOfMonth = date('Y-m-t 23:59:59', strtotime($startDateOfTheDay));
        $where3 = "blqr.child_id = $childId and blqr.request_date >= '$startDateOfMonth' and blqr.request_date <= 
					'$endDateOfMonth' and blqr.grade_id = $grade and blqr.request_type = 'W'";
        $select3 = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
                    "SUM(if(blqr.device_response_time = 0,1,0)) as UnAnswered",
                    "SUM(if(blqr.points_type = 'A',blqr.points,0)) as CorrectAnswerPoints",
                    "SUM(if((blqr.points_type = 'D'OR
(blqr.request_type = 'W' AND blqr.points IS NOT NULL AND blqr.points_type IS NULL)),blqr.points,0)) as WrongAnswerPoints"
                ))->where($where3);
        $resMonth = $db->fetchRow($select3);
        $resultArray = array('thisWeekChallengeData' => $resWeek, 'todayChallengeData' => $resToday, 'monthChallengeData' => $resMonth);
        return $resultArray;
    }

    /**
     * function to get subject list of child
     * @param int gradeId,childId
     * @return Array list
     */
    public function getSubjectListOnGradeOfChild($gradeId, $childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();


        $select = $db->select()->from(array(
                    'blqr' => 'bal_child_question_requests'
                        ), array(
                    "request_id"
                ))->joinLeft(array(
                    'bcq' => 'bal_child_questions'
                        ), 'blqr.request_id = bcq.request_id', array(
                    "bcq.question_id",
                ))->joinLeft(array(
                    'blq' => 'bal_questions'
                        ), 'bcq.question_id = blq.bal_question_id', array(
                    'blq.category_id'
                ))->joinLeft(array(
                    'blcg' => 'bal_question_category_grades'
                        ), "blcg.category_id = blq.category_id", array(
                    'blcg.category_id as cateId'
                ))->joinLeft(array('blc' => 'bal_question_categories'), 'blcg.category_id = blc.category_id', array('category_id'))
                ->joinLeft(array('blg' => 'bal_grades'), 'blg.grades_id = blcg.grade_id', array(
                    'grade_name'
                ))->joinLeft(array('bls' => 'bal_subjects'), 'bls.subject_id = blc.subject_id', array(
                    'bls.*'
                ))->joinLeft(array('blst' => 'bal_standards'), 'blst.standard_id = blc.standard_id', array(
                    'blst.standard_id'
                ))
                ->where("blqr.child_id = '$childId'")
                //->where("blst.standard_id='".STANDARD."'")
                ->group('bls.subject_id');
        $subjectwiseData = $db->fetchAll($select);

        return $subjectwiseData;
    }

    /**
     * function to get grade list of child
     * @param int childId
     * @return Array list
     * @author Suman Khatri on 28th january 2014
     */
    public function getQuestionAskedGradeOfChild($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array(
                            'blqr' => 'bal_child_question_requests'
                                ), array(
                            "blqr.grade_id"
                        ))->joinLeft(array('blg' => 'bal_grades'), 'blqr.grade_id = blg.grades_id', array('blg.grades_id', 'blg.grade_name'))
                        ->where("blqr.child_id = '$childId'")
                        ->group('blqr.grade_id')->order("blqr.request_date desc");
        $gradeData = $db->fetchAll($select);
        return $gradeData;
    }

    /**
     * function to get total askedquestion with their answer type in a turn
     * @param int $rId
     * @retrun array
     * @author suman khatri on 29th March 2014
     */
    public function getTotalQuestionUsingRequestId($rId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('totalA' => $this->_name), array("SUM(if(totalA.points_type = 'A',1,0)) AS totalCorrect",
                    "SUM(if(totalA.points_type = 'D',1,0)) AS totalIncorrect",
                    "SUM(if(totalA.points_type = 'NULL',1,0)) AS totalUnanswered"))
                ->where("totalA.request_id  = $rId or totalA.reference_request_id  = $rId")
                ->where("totalA.device_response_time  is not null and totalA.device_response_time != 0");
        $totalPoints = $db->fetchRow($select);
        return $totalPoints;
    }

    /**
     * function to get referenceId
     * @param int $rId
     * @retrun array
     * @author suman khatri on 29th March 2014
     */
    public function getReferenceIdUsingRequestId($rId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('totalA' => $this->_name), array("totalA.reference_request_id"))
                ->where("totalA.request_id  = $rId");
        $questionData = $db->fetchRow($select);
        return $questionData;
    }

    /*     * *************
     * function for delete child question requests
     * @param childId Int
     * return int
     *
     * ************************ */

    public function deleteData($childId)
    {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->delete($where);
    }

    /**
     * function to get list of already asked question for a child in question fetch logic
     * @param int childId
     * @return Array
     */
    public function getAskedQuestionToFetchQuestionIds($childId, $seq = null, $gradId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array(''))
                ->joinInner(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id 	', array('bcq.question_id'))
                ->where("blqr.child_id = '$childId'")
                ->group("bcq.question_id");
        $questionid = $db->fetchAll($select);
        return $questionid;
    }

    public function getAllLastWeekSubjectsOfChild($weeklyGoalDateRange, $childId, $gradeId)
    {
        if (!empty($weeklyGoalDateRange)) {
            $startDate = $weeklyGoalDateRange['stratDate'];
            $endDate = $weeklyGoalDateRange['endDate'];
            $where = $this->_db->quoteInto("blqr.request_date >= ?", $startDate);
            $where .= $this->_db->quoteInto(" and blqr.request_date <= ?", $endDate);
        } else {
            $where = 1;
        }
        $whereChild = $this->_db->quoteInto("blqr.child_id = ?", $childId);
        $whereChild .= $this->_db->quoteInto(" and blqr.grade_id = ?", $gradeId);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array(''))
                ->joinInner(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array(''))
                ->joinInner(array('blq' => 'bal_questions'), 'bcq.question_id = blq.bal_question_id', array(''))
                ->joinInner(array('blc' => 'bal_question_categories'), 'blq.category_id = blc.category_id', array('blc.subject_id'))
                ->joinInner(array('bls' => 'bal_subjects'), 'bls.subject_id = blc.subject_id', array('bls.subject_name'))
                ->where($whereChild)
                ->where($where)
                ->group("blc.subject_id");
        $subjectIds = $db->fetchAll($select);
        return $subjectIds;
    }

    public function getLastWeekPointSubjectWise($weeklyGoalDateRange, $subjectId, $childId, $gradeId)
    {
        if (!empty($weeklyGoalDateRange)) {
            $startDate = $weeklyGoalDateRange['stratDate'];
            $endDate = $weeklyGoalDateRange['endDate'];
            $where = $this->_db->quoteInto("blqr.request_date >= ?", $startDate);
            $where .= $this->_db->quoteInto(" and blqr.request_date <= ?", $endDate);
            if (!empty($subjectId)) {
                $where .= $this->_db->quoteInto(" and blc.subject_id = ?", $subjectId);
            }
        } else {
            $where = 1;
        }
        $whereChild = $this->_db->quoteInto("blqr.child_id = ?", $childId);
        $whereChild .= $this->_db->quoteInto(" and blqr.grade_id = ?", $gradeId);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_question_requests'), array("SUM(if(blqr.points_type = 'A',1,0)) AS totalCorrect",
                    "SUM(if(blqr.points_type = 'D',1,0)) AS totalIncorrect",
                    "SUM(if(blqr.points_type = 'NULL',1,0)) AS totalUnanswered"))
                ->joinInner(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array(''))
                ->joinInner(array('blq' => 'bal_questions'), 'bcq.question_id = blq.bal_question_id', array(''))
                ->joinInner(array('blc' => 'bal_question_categories'), 'blq.category_id = blc.category_id', array(''))
                ->where($whereChild)
                ->where($where)
                ->group("blc.subject_id");
        $subjectData = $db->fetchRow($select);
        return $subjectData;
    }

    /**
     * get best/worst subjects of a kid
     * 
     * @param int $child_id
     * @param bool $topOrder
     * @param int $count
     * @return Zend_Db_Table_Rowset
     */
    public function getSubjectReportLastWeek($child_id, $topOrder = true, $count = 4)
    {

        // main query, get subject wise percentage for last week
        $query = $this->select()->setIntegrityCheck(FALSE);

        $query->from(array('cqr' => 'bal_child_question_requests'), '');
        $query->join(array('cq' => 'bal_child_questions'), 'cqr.request_id = cq.request_id', '');
        $query->join(array('q' => 'bal_questions'), 'cq.question_id = q.bal_question_id', '');
        $query->join(array('qc' => 'bal_question_categories'), 'qc.category_id = q.category_id', '');

        $query->columns(array(
            'DAYOFWEEK(request_date) as week_day', 'qc.subject_id',
            new Zend_Db_Expr('SUM(IF(points_type = "A", 1, 0)) as correct_response'),
            new Zend_Db_Expr('COUNT(*) as total_response')
        ));

        //$query->where('response_date IS NOT NULL');
        $query->where(new Zend_Db_Expr('request_date >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY'));
        $query->where(new Zend_Db_Expr('request_date < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY'));
        $query->where('child_id = ?', $child_id);

        // clonning the main query
        $query1 = clone $query;
        $query2 = clone $query;

        // get final four suject top/focus with percentage (weekly)
        $subjectPercentage = $this->select()->setIntegrityCheck(false);
        $subjectPercentage->from(array('subject_percentage_table' => $query1), 'subject_id');
        $subjectPercentage->columns(array(
            new Zend_Db_Expr('(correct_response/total_response)*100 as subject_percentage')
        ));
        $subjectPercentage->limit($count);

        if ($topOrder) {
            $subjectPercentage->where('(correct_response/total_response)*100 >= ?', 60);
            $subjectPercentage->order('subject_percentage DESC');
        } else {
            $subjectPercentage->where('(correct_response/total_response)*100 < ?', 60);
            $subjectPercentage->order('subject_percentage ASC');
        }

        $query1->group('subject_id');

        // get final four subject top/focus with percentage daily
        $dailyQuery = $this->select()->setIntegrityCheck(false);
        $dailyQuery->from(array('daily_percentage_table' => $query2));
        $dailyQuery->join(array('subject_percentage_table' => $subjectPercentage), 'daily_percentage_table.subject_id = subject_percentage_table.subject_id', 'subject_percentage');
        $dailyQuery->join(array('subjects' => 'bal_subjects'), 'subjects.subject_id = daily_percentage_table.subject_id', 'subject_name');
        $dailyQuery->columns(array(
            new Zend_Db_Expr('(correct_response/total_response)*100 as daily_percentage')
        ));

        $query2->group(array('subject_id', 'week_day'));

        if ($topOrder) {
            $dailyQuery->order('subject_percentage DESC');
        } else {
            $dailyQuery->order('subject_percentage ASC');
        }
        $dailyQuery->order('week_day');
        
        //echo $dailyQuery; echo "<br><br>";

        return $this->fetchAll($dailyQuery);
    }
    
    /**
     * get last question asked dated
     * @param int $childId
     * @return array
     */
    public function getLastQuestionAsked($childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('blqr' => 'bal_child_question_requests'), array('request_date'))
            ->where($this->_db->quoteInto("child_id = ?", $childId))
            ->order('blqr.request_date DESC')
            ->limit(1);
        return $questiondata = $db->fetchAll($select);
    }

}
