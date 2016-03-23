<?php

/*
 * This is a model class for Question Categories
 * Created By Sunil Khanchandani
 *
 */

class Application_Model_DbTable_Trophiessub extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_subject_trophies';

    //function to add categories
    public function addTrophy($data) {

        $options = $this->insert($data);

        return $options;
    }

    //function to update categories Data
    public function updatetrophyData($trophyDataArray, $trophy_id) {
        $where = $this->_db->quoteInto("subject_trophy_id 	=?", $trophy_id);
        $updateCatData = $this->update($trophyDataArray, $where);
        return $updateCatData;
    }

    //this function is used to fetch category info
    public function getTrophiesInfo($searchData = null, $grade = null) {
        if ($searchData != null) {

            $where = "bl.title LIKE " . $this->_db->quote('%' . $searchData . '%') . "";
            $where .= " OR bl.description LIKE " . $this->_db->quote('%' . $searchData . '%') . "";
            $arr = explode('-', $searchData);
            $where .= " OR std.subject_name LIKE " . $this->_db->quote('%' . $arr[0] . '%') . "";
            $where .= " OR bl.points LIKE " . $this->_db->quote($searchData) . "";
        } else {
            $where = 1;
        }

        if(!empty($grade)) {
            $where = '('.$where.') AND grade_id = '.$grade;
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_subject_trophies'), array('bl.*'))
                ->joinLeft(array('std' => 'bal_subjects'), 'bl.subject_id = std.subject_id', array('std.subject_name'))
                ->where($where)
                //->group('grds.grade_name')
                ->order('created_date desc');
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    //function to get domain info
    public function deleteTrophy($trophy_id) {

        $where = $this->_db->quoteInto("subject_trophy_id=?", $trophy_id);
        $deleteCategory = $this->delete($where);
        if ($deleteCategory) {

            return true;
        }
    }

    public function trophyInfo($trophy_id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $this->_db->quoteInto("subject_trophy_id=?", $trophy_id);
        $select = $db->select()
                ->from($this->_name)
                ->where($where);
        $categoryData = $db->fetchRow($select);

        return $categoryData;
    }

    public function getsubject_standard() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('sub' => 'bal_subjects'), array('sub.*'))
                ->joinLeft(array('std' => 'bal_standards'), 'sub.standard_id = std.standard_id', array('std.*'));
        /* 	$sql = $select->__toString();
          echo "$sql\n"; */
        $data = $db->fetchAll($select);
        return $data;
    }

    public function getparticularsubject_standard($sub_id) {

        $db = Zend_Db_Table::getDefaultAdapter();

        $where = $this->_db->quoteInto("subject_id=?", $sub_id);
        $select = $db->select()
                ->from(array('sub' => 'bal_subjects'), array('sub.*'))
                ->joinLeft(array('std' => 'bal_standards'), 'sub.standard_id = std.standard_id', array('std.*'))
                ->where($where);
        /* 	$sql = $select->__toString();
          echo "$sql\n"; */
        $data = $db->fetchAll($select);
        return $data;
    }

    //function to get trophy according to subject and points
    public function GetTrophyForsubjectPoints($subId, $points, $grade) {
        $where = "subject_id = $subId and points <= $points and grade_id = $grade";
        $order = "points desc";
        return $this->fetchAll($where, $order)->toArray();
    }

    public function check_title($subject, $title) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $where[] = $this->_db->quoteInto("title=?", $title);
        $where[] = $this->_db->quoteInto("subject_id=?", $subject);
        //$where[] = $this->_db->quoteInto("subject_trophy_id!=?",$id);
        $cond = implode(' and ', $where);


        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_points($subject, $points, $grade) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where[] = $this->_db->quoteInto("points=?", $points);
        $where[] = $this->_db->quoteInto("subject_id=?", $subject);
        $where[] = $this->_db->quoteInto("grade_id=?", $grade);
        //$where[] = $this->_db->quoteInto("subject_trophy_id!=?",$id);
        $cond = implode(' and ', $where);

        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_title2($subject, $title, $id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $where[] = $this->_db->quoteInto("title=?", $title);
        $where[] = $this->_db->quoteInto("subject_id=?", $subject);
        $where[] = $this->_db->quoteInto("subject_trophy_id!=?", $id);
        $cond = implode(' and ', $where);


        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_points2($subject, $points, $grade, $id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where[] = $this->_db->quoteInto("points=?", $points);
        $where[] = $this->_db->quoteInto("subject_id=?", $subject);
        $where[] = $this->_db->quoteInto("grade_id=?", $grade);
        $where[] = $this->_db->quoteInto("subject_trophy_id!=?", $id);
        $cond = implode(' and ', $where);

        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

}
