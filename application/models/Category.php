<?php

class Application_Model_Category extends Zend_Loader_Autoloader {
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblCateGrade;
    private $_tblGrade;
    private $_tblCategory;
    private $_tblSequence;
    private $_tblSequenceTrack;
    private $_tblSubjects;
    private $_tblFrameworks;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        //creates object of model file QuestionCategoryGrade
        $this->_tblCateGrade = new Application_Model_DbTable_QuestionCategoryGrade ();
        //creates object of model file ChildGrade
        $this->_tblGrade = new Application_Model_DbTable_ChildGrade ();
        //creates object of model file QuestionCategory
        $this->_tblCategory = new Application_Model_DbTable_QuestionCategories ();
        //creates object of model file ChildQuestionSequence
        $this->_tblSequence = new Application_Model_DbTable_ChildQuestionSequence ();
        //creates object of model file ChildQuestionSequenceTrack
        $this->_tblSequenceTrack = new Application_Model_DbTable_ChildQuestionSequenceTrack ();
        //creates object of model file ChildSubject
        $this->_tblSubjects = new Application_Model_DbTable_ChildSubject ();
        //creates object of model file Framework
        $this->_tblFrameworks = new Application_Model_DbTable_Framework ();
    }

    /**
     * @desc Function to get category ids
     * @param gardeId,childId,subjectID
     * @author suman khatri on 13th November 2013
     * @return array
     */
    public function getChildCategoryIds($gradeId, $childId, $subjectId) {
        //getting categories on basis of provided params
        $getChildCategoryList = $this->_tblCateGrade->getCategoryListOnGradeOfChild($gradeId, $childId, $subjectId);
        $i = 0;
        $catId = array(); //initialize array
        foreach ($getChildCategoryList as $childCat) {
            $catId[$i] = $childCat ['category_id'];
            $i++;
        }
        return $catId; //returns cateId
    }

    /**
     * @desc Function to get subject list of child according to grade
     * @param gardeId,childId,subjectID
     * @author suman khatri on 13th November 2013
     * @return array
     */
    public function getSubjectListOfChildWithGrade($gradeId, $childId, $returnType) {
        //getting selected subject list of child 
        $getChildSubjectList = $this->_tblCateGrade->getSubjectListOnGradeOfChild($gradeId, $childId);
        if ($returnType == 'array') {
            $i = 0;
            $subId = array(); //initialize array
            foreach ($getChildSubjectList as $childSub) {
                $subId[$i] = $childSub ['subject_id'];
                $i++;
            }
            $resUlt = $subId;
        } else if ($returnType == 'string') {
            foreach ($getChildSubjectList as $childSub) {
                if (empty($subId)) {
                    $subId = "'" . $childSub ['subject_id'] . "'";
                } else {
                    $subId = $subId . ",'" . $childSub ['subject_id'] . "'";
                }
            }
            $resUlt = array('subjectList' => $getChildSubjectList, 'subId' => $subId);
        }
        return $resUlt; //returns result
    }

    /**
     * @desc Function to get other subject list according of grade which are not selected by child
     * @param gardeId,subId
     * @author suman khatri on 13th November 2013
     * @return array
     */
    public function getOtherSubjectListGradeWiseNotSelectedByChild($gradeId, $subId) {
        //getting selected subject list grade wise 
        $SubjectList = $this->_tblCateGrade->getAllSubjectListOnGrade($gradeId, $subId);
        return $SubjectList; //returns $SubjectList
    }

    /**
     * @desc Function to format subjectlist array for questionlog page
     * @param $getChildSubjectList,$SubjectList
     * @author suman khatri on 13th November 2013
     * @return array
     */
    public function getSubjectListPageForQuestionlog($getChildSubjectList, $SubjectList) {
        //getting formated subject list for question lpg page
        $subCount = count($getChildSubjectList);
        $allsubCount = count($SubjectList);
        $subjectListFinal = array(
            'child_subject_list' => $getChildSubjectList,
            'child_total_subject' => $subCount,
            'all_subject_list' => $SubjectList,
            'all_total_count' => $allsubCount
        );
        return $subjectListFinal; //returns $subjectListFinal
    }

    /**
     * @desc Function to format subjectlist array for questionlog page
     * @param $subjectId, $gradeId
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getCategoryUsingSpecifiedParams($subjectId = null, $gradeId = null, $childId = null) {
        $categoryId = '';
        if ((!empty($subjectId) && $subjectId != null) || (!empty($gradeId) && $gradeId != null)) {
            $Catagorydata = $this->_tblCategory->getCategoryByGradeandFrameworkForParentLog($subjectId, $gradeId);
        }
        if (!empty($childId) && $childId != null) {
            $Catagorydata = $this->_tblCategory->getCategoryByChild($childId);
        }
        foreach ($Catagorydata as $cData) {
            if ($categoryId == '') {
                $categoryId = $cData ['category_id'];
            } else {
                $categoryId = $categoryId . ' , ' . $cData ['category_id'];
            }
        }
        return $categoryId;
    }

    /**
     * @desc Function to subject info for questionlog page
     * @param $subjectId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function getSubjectInfoForQuestionlog($subjectId) {
        $whereframe = "subject_id in ($subjectId)";
        $frameworkData = $this->_tblFrameworks->fetchAll($whereframe)->toArray();
        if (!empty($frameworkData) && count($frameworkData) == 1) {
            $data = $frameworkData;
        } else {
            $frameworkData1 [0] ['subject_name'] = 'All';
            $data = $frameworkData1;
        }
        return $data;
    }

    /**
     * @desc Function to grade info for questionlog page
     * @param $subjectId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function getGradeInfoForQuestionlog($gradeId) {
        $wheregrade = "grades_id in ($gradeId)";
        $gradeData = $this->_tblGrade->fetchAll($wheregrade)->toArray();
        if (!empty($gradeData) && count($gradeData) == 1) {
            $data = $gradeData;
        } else {
            $gradeData1 [0] ['grade_name'] = 'All';
            $data = $gradeData1;
        }
        return $data;
    }

    /**
     * @desc Function to get current sequesnce of child
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getCurrentSequenceOfChild($childId) {
        //fetches current sequence record
        $sequenceInfo = $this->_tblSequenceTrack->getCurrentsequencenumber($childId);
        return $sequenceInfo; //returns array
    }

    /**
     * @desc Function to get max sequesnce of child
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getMaxSequenceOfChild($childId) {
        //fetches max sequence record
        $maxsequenceInfo = $this->_tblSequence->getMaxsequence($childId);
        return $maxsequenceInfo; //returns array
    }

    /**
     * @desc Function to get min sequesnce of child
     * @param $childId
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getMinSequenceOfChild($childId) {
        $minsequenceInfo = $this->_tblSequence->getMinsequence($childId); //fetches min sequence record
        return $minsequenceInfo; //returns array
    }

    /**
     * @desc Function to get next sequesnce of child
     * @param $childId,$currentSequence
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getNextSequenceOfChild($currentSequence, $childId) {
        //fetches next sequence record
        $nextsequenceInfo = $this->_tblSequence->getNextSequence($currentSequence, $childId);
        return $nextsequenceInfo; //returns array
    }

    /**
     * @desc Function to update sequesnce of child
     * @param $nextSequence,$childId,$action
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function addOrUpdateSequenceOfChild($nextSequence, $childId, $action) {
        if ($action == 'update') {
            //updates sequence record
            $sequenceTrackData = array(
                'current_sequence_number' => $nextSequence
            );
            $seqTrack = $this->_tblSequenceTrack->updateData($sequenceTrackData, $childId);
        }
        if ($action == 'insert') {
            //insert sequence record
            $sequenceTrackData = array(
                'child_id' => $childId,
                'current_sequence_number' => $nextSequence
            );
            $seqTrack = $this->_tblSequenceTrack->addSequenceTrack($sequenceTrackData);
        }
        return $seqTrack; //returns result
    }

    /**
     * @desc Function to remove child's sequence
     * @param $childId
     * @author suman khatri on 19th November 2013
     * @return result
     */
    public function removeChildSequence($childId) {
        //remove child old subject
        $result = $this->_tblSequence->removeSequence($childId);
        return $result; //return result; 
    }

    /**
     * @desc Function to get categorys
     * @param $standard_id, $framework_id, $gradeId, $domain_id
     * @author suman khatri on 19h November 2013
     * @return ArrayIterator
     */
    public function getCategory($standardId, $frameworkId, $gradeId, $domainId) {
        //fetches category according to specified param  
        $categoryData = $this->_tblCategory->getCategory($standardId, $frameworkId, $gradeId, $domainId);
        return $categoryData; //returns array
    }

    /**
     * @desc Function to add sequence for child
     * @param $childId,$categoryId,$j,$date
     * @author suman khatri on 19h November 2013
     * @return result
     */
    public function addSequenceForChild($childId, $categoryId, $j, $date) {
        //data to be insert
        $arraySequence = array(
            'child_id' => $childId,
            'category_id' => $categoryId,
            'sequence_number' => $j,
            'created_date' => $date
        );
        //insert sequence into DB 
        $result = $this->_tblSequence->addSequence($arraySequence);
        return $result; //returns result
    }

    /**
     * @desc Function to get all subject list
     * @param nill
     * @author suman khatri on 19h November 2013
     * @return ArrayIterator
     */
    public function getAllSubject() {
        $getSubjectList = $this->_tblSubjects->getAllSubjectList(); //fetches all subject list
        return $getSubjectList; //returns array
    }

    /**
     * @desc Function to get all grade list
     * @param nill
     * @author suman khatri on 19h November 2013
     * @return ArrayIterator
     */
    public function getAllGrade() {
        $getGradeList = $this->_tblGrade->getAllGradeList(); // fetches all grade list
        return $getGradeList; //return array
    }

    /**
     * @desc Function to get all subject list grade wise
     * @param gardeId
     * @author suman khatri on 19th November 2013
     * @return array
     */
    public function getSubjectListGradeWise($gradeId, $isBibleOnly = false, $noBibleQuestions = false) {
        //getting subject list grade wise 
        $getSubjectList = $this->_tblCateGrade->getSubjectListOnGrade($gradeId, $isBibleOnly, $noBibleQuestions); //fetches subject list of grade
        $subjectDomainArray = array();
        //getting all domain related to the subject and grade
        foreach ($getSubjectList as $subjectData){
                $subjectDomainArray[$subjectData['subject_id']]['subject_id'] = $subjectData['subject_id'];
                $subjectDomainArray[$subjectData['subject_id']]['subject_name'] = $subjectData['subject_name'];
                $subjectDomainArray[$subjectData['subject_id']]['domainArray'][$subjectData['domain_id']]['domain_id'] = $subjectData['domain_id'];
                $subjectDomainArray[$subjectData['subject_id']]['domainArray'][$subjectData['domain_id']]['code'] = $subjectData['code'];
                $subjectDomainArray[$subjectData['subject_id']]['domainArray'][$subjectData['domain_id']]['name'] = $subjectData['name'];
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
     * @desc Function to get categoryId
     * @param $categoryName
     * @author suman khatri on 19th November 2013
     * @return id
     */
    public function getcategoryId($categoryName) {
        $catId = $this->_tblCategory->getcategoryId($categoryName);
        return $catId;
    }

    /**
     * @desc Function to get categoryinfo
     * @param $categoryId
     * @author suman khatri on 19th November 2013
     * @return array
     */
    public function getCategoryInfo($categoryId) {
        $cateInfo = $this->_tblCategory->categoryInfo($categoryId);
        return $cateInfo;
    }
    
    /**
     * @desc Function to get categories having specified grade and subject
     * @param $subjectId, $gradeId
     * @author suman khatri on 1st May 2014
     * @return ArrayIterator
     */
    public function checkCategoryUsingGradeAndSubject($subjectId = null, $gradeId = null, $doimanId = null) {
        //getting categories having subject and grade
        if ((!empty($subjectId) && $subjectId != null) || (!empty($gradeId) && $gradeId != null)) {
            $catagorydata = $this->_tblCategory->getCategoryByGradeandFrameworkForParentLog($subjectId, $gradeId,$doimanId);
        }
        return $catagorydata;
    }
    
    /**
    * @desc function to get all domains by grade and subject
    * @param int gradeid,framework_id,standard_id
    * @return ArrayIterator
    * @author Suman Khatri on 19th June 2014
    */
    public function getAllDomainsUsingGradeAndSubject($gradeId,$subjectId)
    {
        $allDomainInfo = $this->_tblCategory->getdomainInfobyGradeAndSubject($gradeId,$subjectId); //getting all domains
        $allDomainInfo = array_map('current', $allDomainInfo);
        //$domainseri = implode(",", $allDomainInfo);
        return $allDomainInfo;
    }
    
     /**
    * @desc function to remove sequence track of child
    * @param int child_id
    * @return no of affected rows
    * @author Suman Khatri on 19th June 2014
    */
    public function removeSequenceTrack($childId)
    {
        $result = $this->_tblSequenceTrack->deleteData($childId);
        return $result; //returns $result
    }
    
    
    /**
    * @desc function to get all domains by grade and subject
    * @param int gradeid,framework_id,standard_id
    * @return ArrayIterator
    * @author Suman Khatri on 19th June 2014
    */
    public function getAllDomainsFullInfoUsingGradeAndSubject($gradeId,$subjectId)
    {
        $allDomainInfo = $this->_tblCategory->getdomainFullInfobyGradeAndSubject($gradeId,$subjectId); //getting all domains
        //$allDomainInfo = array_map('current', $allDomainInfo);
        //$domainseri = implode(",", $allDomainInfo);
        return $allDomainInfo;
    }
   
}
