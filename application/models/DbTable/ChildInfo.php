<?php

/*
 * This is a model class for Child Information
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */

class Application_Model_DbTable_ChildInfo extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_children';

    /*
     * This is a function to check the child exist for parent or not
     */

    public function childExist($parentId) {
        $where = $this->_db->quoteInto("parent_id =?", $parentId);
        $childExist = $this->fetchRow($where);
        if ($childExist) {
            return true;
        } else {
            return false;
        }
    }

    public function childExistWithParChildId($parentId, $childId) {
        $where = $this->_db->quoteInto("parent_id =?", $parentId);
        if (!empty($childId)) {
            $where .= $this->_db->quoteInto(" AND child_id =?", $childId);
        }
        $childExist = $this->fetchRow($where);
        if ($childExist) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This is a function to add child
     * 
     */

    public function addChild($childInfo) {

        return $this->insert($childInfo);
    }

    /**
     * This is a function to update child Info
     */
    public function updateChildInfo($updateData, $childId) {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->update($updateData, $where);
    }

    //this function is used to fetch child info according to parent id
    public function getChildbasicinfo($parId, $selectFields = array(), $sortBy = 'name', $sortOrder = 'ASC') {

        if (!empty($selectFields)) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array("bl" => "bal_children"), $selectFields);
            $select->where("bl.parent_id = ? ", $parId);
            $select->order("bl.$sortBy $sortOrder");
            $childInfo = $db->fetchAll($select);
        } else {
            $where = "parent_id = $parId";
            $order = "$sortBy $sortOrder";
            $childInfo = $this->fetchAll($where, $order);
        }

        return $childInfo;
    }
    
    //this function is used to fetch single child with name asc
    public function getSingleChildByNameAsc($parId, $selectFields = array()) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
            
        $select->from(array("bl" => "bal_children"));
        if (!empty($selectFields)) {
            $select->from(array("bl" => "bal_children"), $selectFields);    
        }
        
        $select->where("bl.parent_id = ? ", $parId);
        $select->order("bl.name ASC");
        $select->limit("1");
        $childInfo = $db->fetchRow($select);
        
        return $childInfo;
    }
    
    //this function is used to fetch single child with name asc
    public function getSingleChildDetailsByNameAsc($parId, $selectFields = array()) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
            
        $select->from(array("bl" => "bal_children"));
        if (!empty($selectFields)) {
            $select->from(array("bl" => "bal_children"), $selectFields);    
        }
        $select->joinLeft(array('grad' => 'bal_grades'), 'bl.grade_id = grad.grades_id');
        
        $select->where("bl.parent_id = ? ", $parId);
        $select->order("bl.name ASC");
        $select->limit("1");
        $childInfo = $db->fetchRow($select);
        
        return $childInfo;
    }

    //this function is used to fetch child info according to parent id
    /* public function getChildinfo($parId)
      {
      $parId = '5';
      $db = Zend_Db_Table::getDefaultAdapter();
      $select = $db->select()
      ->from(array('bl' => 'bal_children'),
      array('bl.*'))
      ->joinLeft(array('bli' => 'bal_child_goals'),
      'bl.child_id = bli.child_id')
      ->joinLeft(array('chquerq' => 'bal_child_question_requests'),
      'bl.child_id = chquerq.child_id')
      ->joinLeft(array('bcq' => 'bal_child_questions'),
      'chquerq.request_id = bcq.request_id')
      ->where('bl.child_id = ? ',$parId);
      $childInfo =  $db->fetchAll($select);
      return $childInfo;
      } */


    //function is used to get child info
    public function getChildInfo($childId) {
        $where = "child_id = $childId";
        return $this->fetchRow($where);
    }

    //this function is used to fetch child info according to child id
    public function getChildBasicsinfo($childId) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_children'), array('bl.*'))
                ->joinLeft(array('bli' => 'bal_child_goals'), 'bl.child_id = bli.child_id', array('child_goal_id', 'question_popup_time', 'number_of_questions', 'weekly_points', 'unlock_time', 'learning_type', 'is_interrupt'))
                ->joinLeft(array('chddevice' => 'bal_child_devices'), 'bl.child_id = chddevice.child_id', array('child_device_id', 'phone_number', 'unique_key', 'device_key'))
                ->joinLeft(array('grad' => 'bal_grades'), 'bl.grade_id = grad.grades_id')
                ->where('bl.child_id = ? ', $childId);
        $childInfo = $db->fetchRow($select);
        return $childInfo;
    }

    //function to get childs list on basis of grade and subject
    public function GetChildInfogradewise($gradeId, $subjectId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.*'))
                ->joinLeft(array('bcs' => 'bal_child_subjects'), 'bc.child_id = bcs.child_id', array('subject_id'))
                ->where('bc.grade_id = ? ', $gradeId)
                ->where('bcs.subject_id = ? ', $subjectId);
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    /*     * *************function for fetch gerade id
     *  for check garade id is equal to or not
     *  @param int child_id
     *  @return array;
     *  ******** */

    public function getGradeId($childId) {
        $where = "child_id = $childId";
        $result = $this->fetchRow($where);
        return $result;
    }

    /**
     * function used to get child info for particular parent
     * @param INT parId
     * @return Array List
     * @author Suman khatri 
     */
    public function getChildinfoForParent($parId, $searchData = NULL, $childId = NULL) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $searchCond = '1';
        if (!empty($searchData)) {
            $searchCond = "CONCAT(bl.name) like '%$searchData%' OR bl.grade_id like '%$searchData%'";
        }

        $select = $db->select();
        $questionSubquery = '';
        $selectAry = array('bl.*');

        //if($parId == 0)
        {
            $questionSubquery = "(SELECT COUNT(1) FROM `bal_child_question_requests` AS br 
    where br.child_id = bl.child_id AND br.request_type = 'Q' GROUP BY `br`.`child_id`)";

            $selectAry['totalquestion'] = new Zend_Db_Expr($questionSubquery);

            $questionSubquery = "(SELECT COUNT(1) FROM `bal_child_question_requests` AS br 
    where br.child_id = bl.child_id AND br.request_type = 'P' GROUP BY `br`.`child_id`)";

            $selectAry['totalquizquestion'] = new Zend_Db_Expr($questionSubquery);
        }

        $select->from(array('bl' => 'bal_children'), $selectAry
        );
        $select->joinLeft(array('grad' => 'bal_grades'), 'bl.grade_id = grad.grades_id', array('grad.grade_name'));
        
        if(!empty($childId)){
            $select->where('bl.child_id = ? ', $childId);
        }

        $select->where('bl.parent_id = ? ', $parId)->where($searchCond);

        $select->order('bl.created_date desc');

        //echo $select; exit;

        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    /**
     * @desc Function to check child's first name 
     * @param $childId,$childName,$parId
     * @author suman khatri on October 01 2014
     * @return true or false
     */
    public function checkChildName($childId, $childName, $parId) {
        $where = $this->_db->quoteInto("parent_id = ?", $parId);
        $where .= $this->_db->quoteInto(" and firstname = ?", $childName);
        if (!empty($childId) && $childId != null && $childId != 'undefined') {
            $where .= $this->_db->quoteInto(" and child_id != ?", $childId);
        }
        $childInfo = $this->fetchRow($where);
        if (empty($childInfo) || $childInfo == null) {
            return false;
        } else {
            return true;
        }
    }

    /*     * *************
     * function for delete child info
     * @param childId Int
     * return int
     *
     * ************************ */

    public function deleteData($childId) {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->delete($where);
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_children'), array('COUNT(bl.child_id) as count'))
                ->where("bl.parent_id != 0")
                ->having("COUNT(child_id) >= 1")
                ->order('count')
                ->group('bl.parent_id');
        $allData = $db->fetchAll($select);
        return $allData;
    }

    /*     * ******************function for 
     * geting total count of child************** */

    public function getTotalFinnyChild() {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('this' => $this->_name), array("SUM(if(this.parent_id > '0',1,0)) AS totalFinnyKid",
            "SUM(if(this.parent_id = '0',1,0)) AS totalFinnyLiteKid"));
        $result = $db->fetchRow($select);
        return $result;
    }

    /*     * ******************function for 
     * geting total count of child************** */

    public function getTotalFinnyLiteChild() {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('this' => $this->_name), array("SUM(if(this.parent_id = '0',1,0)) AS totalFinnyLiteKid"));
        $result = $db->fetchRow($select);
        return $result;
    }

    /**
     * @desc Function to get child and parent info grade wise
     * @param $gradeId
     * @author suman khatri on 1st May 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWise($gradeId, $subjectId = null) {
        if (!empty($subjectId) && $subjectId != null) {
            $where = "bcs.subject_id != $subjectId OR bcs.subject_id IS NULL";
        } else {
            $where = "1";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.child_id', 'bc.gender'))
                //    ->joinLeft(array('bcs' => 'bal_child_subjects'), 'bc.child_id = bcs.child_id', array('bc.child_id'))
                ->joinLeft(array('bp' => 'bal_parents'), 'bc.parent_id = bp.parent_id', array('bp.parent_id'))
                ->joinLeft(array('bu' => 'bal_users'), 'bp.user_id = bu.user_id', array('bu.user_id'))
                ->where('bc.grade_id = ? ', $gradeId)
                //    ->where($where)
                ->where("bc.child_id is not null")
                ->group("bc.child_id");

        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    /**
     * @desc Function to get child and parent info grade wise having specified domain ID
     * @param $gradeId,$subjectId,$domainId
     * @author suman khatri on 24th June 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWiseHavingSpecifiedDomian($gradeId, $subjectId, $domainId) {
        if (!empty($subjectId) && $subjectId != null && !empty($domainId) && $domainId != null) {
            $where = "bcs.subject_id = $subjectId and bcs.domain_id = $domainId";
        } else {
            $where = "1";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.child_id'))
                ->joinLeft(array('bcs' => 'bal_child_subjects'), 'bc.child_id = bcs.child_id', array())
                ->joinLeft(array('bp' => 'bal_parents'), 'bc.parent_id = bp.parent_id', array())
                ->joinLeft(array('bu' => 'bal_users'), 'bp.user_id = bu.user_id', array())
                ->where('bc.grade_id = ? ', $gradeId)
                ->where($where)->where("bc.child_id is not null")
                ->group("bc.child_id");
        $childInfo = $db->fetchAll($select);
        $childId = array_map('current', $childInfo);
        $childIds = implode(',', $childId);
        return $childIds;
    }

    /**
     * @desc Function to get child and parent info grade wise not having specified domain ID
     * @param $gradeId,$subjectId,$domainId
     * @author suman khatri on 24th June 2014
     * @return ArrayIterator
     */
    public function getChildParentInfoGradeWiseNotHavingSpecifiedDomian($gradeId, $subjectId, $domainId, $childIds) {
        if (!empty($subjectId) && $subjectId != null && !empty($domainId) && $domainId != null) {
            //$where = "bcs.subject_id = $subjectId and bcs.domain_id = $domainId";
            $where = "bcs.subject_id = $subjectId";
        } else {
            // $where = "1";
        }
        if (!empty($childIds) && $childIds != null) {
            $where1 = "bc.child_id not in ($childIds)";
        } else {
            $where1 = "1";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.child_id', 'bc.gender'))
                ->joinLeft(array('bcs' => 'bal_child_subjects'), 'bc.child_id = bcs.child_id', array('bc.child_id'))
                ->joinLeft(array('bp' => 'bal_parents'), 'bc.parent_id = bp.parent_id', array('bp.parent_id'))
                ->joinLeft(array('bu' => 'bal_users'), 'bp.user_id = bu.user_id', array('bu.user_id'))
                ->where('bc.grade_id = ? ', $gradeId)
                //->where($where)
                ->where("bc.child_id is not null")
                ->where($where1)
                ->group("bc.child_id");
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    //function to get childs list on basis of grade and subject
    public function GetChildInfoGradeSubjectAndDomainwise($gradeId, $subjectId, $domainId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.*'))
                ->joinInner(array('bcs' => 'bal_child_subjects'), 'bc.child_id = bcs.child_id', array('subject_id'))
                ->where('bc.grade_id = ? ', $gradeId)
                ->where('bcs.subject_id = ? ', $subjectId)
                ->where('bcs.domain_id = ? ', $domainId);
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    /**
     * @desc Function to get all childrens info using parent Id/cjildId
     * @param $parentId,$childId
     * @author suman khatri on October 09 2014
     * @return arrayIterator
     */
    public function getChildrensAllInfoUsingParentIdOrChildId($parentId, $childId)
    {
        if (!empty($parentId) && $parentId != null) {
            $where = $this->_db->quoteInto("bc.parent_id = ?", $parentId);
        }
        if (!empty($childId) && $childId != null) {
            $where = $this->_db->quoteInto("bc.child_id = ?", $childId);
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bc' => 'bal_children'), array('bc.firstname',
                    'bc.lastname',
                    'bc.grade_id',
                    'bc.gender',
                    'bc.dob',
                    'bc.gpa',
                    'bc.school_name',
                    'bc.child_id',
                    'bc.image',
                    'bc.coppa_required',
                    'bc.coppa_accepted',
                    'bc.track_location',
                    'bc.avatar'))
                ->joinLeft(array('bli' => 'bal_child_goals'), 'bc.child_id = bli.child_id', array('bli.question_popup_time',
                    'bli.number_of_questions',
                    'weekly_points', 'unlock_time', 'learning_type', 'is_interrupt'))
                ->joinLeft(array('bcgp' => 'bal_child_grade_points'), 'bc.child_id = bcgp.child_id and bc.grade_id = bcgp.grade_id', array('bcgp.points'))
                ->joinLeft(array('bpcm' => 'bal_parent_custom_message'), 'bc.child_id = bpcm.child_id', array('bpcm.message'))
                ->where($where)
                ->order("bc.name")
                ->group("bc.child_id");
        $childsInfo = $db->fetchAll($select);
        return $childsInfo; //returns all child info
    }

    /**
     * @desc Function to check child's full name 
     * @param $childId,$childName,$parId
     * @author suman khatri on October 01 2014
     * @return true or false
     */
    public function checkChildFullName($childId, $childName, $parId)
    {
        $where = $this->_db->quoteInto("parent_id = ?", $parId);
        $where .= $this->_db->quoteInto(" and name = ?", $childName);
        if (!empty($childId) && $childId != null) {
            $where .= $this->_db->quoteInto(" and child_id = ?", $childId);
        }
        $childInfo = $this->fetchRow($where);
        if (empty($childInfo) || $childInfo == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check is COPPA consent is accepted or not
     * @param Int $childId
     * @return boolean
     */
    public function isCoppaAccepted($childId)
    {
        $data = $this->fetchRow(array('child_id = ?' => $childId, 'coppa_accepted = ?' => TRUE));
        if (!empty($data)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     *Get child basic detail from child id
     *@param Int $childId
     *@return array $childInfo
     *@author: Abhinav
     *@since: 3-Nov-2015
     */
    public function getChildbasicinfoByChildId($childId, $selectFields = array()) {

        if (!empty($selectFields)) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array("bl" => "bal_children"), $selectFields);
            $select->where("child_id = ?", $childId);
            $select->order("bl.name ASC");
            $select->limit("1"); 
            $childInfo = $db->fetchRow($select);
        } else {
            $where = "child_id = $childId";
            $order = "name ASC"; 
            $childInfo = $this->fetchRow($where, $order);
        }
        print_r($childInfo);

        return $childInfo;
    }
}
