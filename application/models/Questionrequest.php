<?php

class Application_Model_Questionrequest extends Zend_Loader_Autoloader {
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblQuestionReq;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        //creates object for model file ChildDeviceInfo
        $this->_tblQuestionReq = new Application_Model_DbTable_ChildQuestionRequest();
        //creates object for model file ParentInfo
    }

    /*
     * functions for geting child total achive points
     * @param int childId
     * @return array;
     * @Dharmendra Mishra
     */

    function getTotalAchivePoints($childId, $weeklyGoalDateRange) {
        return $this->_tblQuestionReq->getTotalAchivePoints($childId, $weeklyGoalDateRange);
    }

    /**
     * @desc Function to get subject list of child according to grade for report page
     * @param gardeId,childId
     * @author suman khatri on 19th December 2013
     * @return array
     */
    public function getSubjectListOfChildWithGradeForReport($gradeId, $childId) {
        //getting selected subject list of child 
        $getChildSubjectList = $this->_tblQuestionReq->getSubjectListOnGradeOfChild($gradeId, $childId);
        $i = 0;
        $subId = array(); //initialize array
        foreach ($getChildSubjectList as $childSub) {
            $subId[$i] = $childSub ['subject_id'];
            $i++;
        }
        $resUlt = $subId;
        return $resUlt; //returns result
    }

    public function getAllLastWeekSubjectsOfChild($weeklyGoalDateRange, $childId, $gradeId) {
        $childSubjects = $this->_tblQuestionReq
                ->getAllLastWeekSubjectsOfChild($weeklyGoalDateRange, $childId, $gradeId);
        return $childSubjects;
    }

    public function getWeeklyPointsForChildSubject($weeklyGoalDateRange, $childId, $gradeId) {
        $childSubjects = $this->getAllLastWeekSubjectsOfChild($weeklyGoalDateRange, $childId, $gradeId);
        $arraySubjectData = array();
        if (!empty($childSubjects)) {
            $i = 0;
            foreach ($childSubjects as $cSubjects) {
                $subjectId = $cSubjects['subject_id'];
                $arraySubjectData[$i]['subjectName'] = $cSubjects['subject_name'];
                $subjectPoints = $this->_tblQuestionReq
                        ->getLastWeekPointSubjectWise($weeklyGoalDateRange, $subjectId, $childId, $gradeId);
                $totalQuestion = $subjectPoints['totalCorrect'] + $subjectPoints['totalIncorrect'] + $subjectPoints['totalUnanswered'];
                $arraySubjectData[$i]['percentage'] = ($subjectPoints['totalCorrect'] / $totalQuestion) * 100;
                unset($subjectPoints);
                $i++;
            }
        }
        return $arraySubjectData;
    }

    public function getTopAndFocusedSubjectOfChildForLastWeek($arraySubjectData) {
        $resultArray = arraymsort($arraySubjectData, array("percentage" => 'SORT_DESC'));
        $i = 0;
        foreach ($resultArray as $rArray) {
            if ($rArray['percentage'] >= 60) {
                if ($rArray['subjectName'] == 'math.content') {
                    $rArray['subjectName'] = 'math';
                }

                if (strlen($rArray['subjectName']) > 11) {
                    $subjectName = substr($rArray['subjectName'], 0, 11) . '...';
                } else {
                    $subjectName = $rArray['subjectName'];
                }
                $topSubject[$i]['subjectNameFull'] = $rArray['subjectName'];
                $topSubject[$i]['subjectName'] = $subjectName;
                $topSubject[$i]['percentage'] = round($rArray['percentage'], 2);
                $i++;
                if ($i == 3) {
                    break;
                }
            }
        }

        $resultArray = arraymsort($arraySubjectData, array("percentage" => 'SORT_ASC'));
        $i = 0;
        foreach ($resultArray as $rArray) {
            if ($rArray['percentage'] < 60) {
                if ($rArray['subjectName'] == 'math.content') {
                    $rArray['subjectName'] = 'math';
                }
                if (strlen($rArray['subjectName']) > 11) {
                    $subjectName = substr($rArray['subjectName'], 0, 11) . '...';
                } else {
                    $subjectName = $rArray['subjectName'];
                }
                $focusSubject[$i]['subjectNameFull'] = $rArray['subjectName'];
                $focusSubject[$i]['subjectName'] = $subjectName;
                $focusSubject[$i]['percentage'] = round($rArray['percentage'], 2);
                $i++;
                if ($i == 3) {
                    break;
                }
            }
        }
        return array('topSubject' => $topSubject, 'focusSubject' => $focusSubject);
    }

    /**
     * Get top scored subjects
     * 
     * @param int $child_id
     * @param int $count
     * @return Array
     */
    public function getTopSubjectLastWeek($child_id, $count = 4) {
        $data = $this->_tblQuestionReq->getSubjectReportLastWeek($child_id, TRUE, $count);
        return $this->formatLastWeekSubjects($data);
    }

    /**
     * Get focus subjects
     * 
     * @param int $child_id
     * @param int $count
     * @return Array
     */
    public function getFocusSubjectLastWeek($child_id, $count = 4) {
        $data = $this->_tblQuestionReq->getSubjectReportLastWeek($child_id, FALSE, $count);
        return $this->formatLastWeekSubjects($data);
    }

    /**
     * Format top/focused subjects last week
     * 
     * @param Zend_Db_Table_Rowset $data
     * @return Array
     */
    public function formatLastWeekSubjects($data) {
        $return = array();
        foreach ($data as $row) {
            if (!isset($return[$row->subject_id])) {
                $return[$row->subject_id] = array(
                    'percentage' => $row->subject_percentage,
                    'title' => $row->subject_name == 'math.content' ? 'Maths' : $row->subject_name,
                    'daily' => array()
                );
            }

            $return[$row->subject_id]['daily'][$row->week_day] = number_format($row->daily_percentage, 2);
        }

        foreach ($return as &$row) {
            $allDays = array_fill(1, 7, 0);
            $row['daily'] = $row['daily'] + $allDays;
            ksort($row['daily']);
        }

        return $return;
    }

}
