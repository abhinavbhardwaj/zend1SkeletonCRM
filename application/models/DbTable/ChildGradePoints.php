<?php

/*
 * This is a model class for Child Information
 * Created By Suman Khatri
 * Thursday, 04 sep 2013 
 */

class Application_Model_DbTable_ChildGradePoints extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_child_grade_points';

    /*
     * This is a function to check the child exist or not
     */

    public function getChildPointsgradewise($childId, $gradeId) {
        $where = "child_id = $childId and grade_id = $gradeId";
        $gradepoints = $this->fetchrow($where);
        return $gradepoints;
    }

    /**
     * This is a function to update child Info
     */
    public function updateChildInfo($updateData, $childId) {
        $where = "child_id = $childId";
        return $this->update($updateData, $where);
    }

    /**
     * function for get gradelist of child in which questions are asked
     * @param int childId
     * @return Array List
     * created by suman on 28 september 2013
     */
    public function GetGradeofChild($childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_grade_points'), array('blqr.*'))
                ->joinLeft(array('blg' => 'bal_grades'), 'blqr.grade_id = blg.grades_id', array('blg.grades_id', 'blg.grade_name'))
                ->where("blqr.child_id = '$childId'")
                ->group('blg.grades_id')
                ->order("blqr.id desc");
        $gradeData = $db->fetchAll($select);
        return $gradeData;
    }

    /*     * *************
     * function for delete child grade points
     * @param childId Int
     * return int
     *
     * ************************ */

    public function deleteData($childId) {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->delete($where);
    }

    /**
     * function for get gradelist of child in which questions are asked
     * @param int childId
     * @return Array List
     * created by suman on 16 june 2014
     */
    public function getChildPointsofCurrentGrade($childId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('blqr' => 'bal_child_grade_points'), array('blqr.*'))
                ->joinLeft(array('blg' => 'bal_grades'), 'blqr.grade_id = blg.grades_id', array('blg.grades_id', 'blg.grade_name'))
                ->where("blqr.child_id = '$childId'")
                //->group('blg.grades_id')
                ->order("blqr.id desc")
                ->limit(1);
        $gradeData = $db->fetchAll($select);
        return $gradeData;
    }

    public function getOverallBoard($childId, $position, $length) {
       // return $this->getLeaderBoard('bal_view_leader_board_overall', $childId, $position, $length);
        return $this->getLeaderBoard('bal_leader_board_overall', $childId, $position, $length);
    }

    public function getWeeklyBoard($childId, $position, $length) {
           return $this->getLeaderBoard('bal_leader_board_weekly', $childId, $position, $length);
    }

    public function getTodayBoard($childId, $position, $length) {
        return $this->getLeaderBoard('bal_leader_board_today', $childId, $position, $length);
    }

    protected function getLeaderBoard($table, $childId, $position, $length) {

        if (empty($childId)) {
            $childId = "NULL";
        }

         $subQueryPosition = $this->select()->setIntegrityCheck(FALSE);
        $subQueryPosition->from($table, array('position', 'points'));
        $subQueryPosition->where("child_id = ?", $childId);

        $subQueryGrade = $this->select()->setIntegrityCheck(FALSE);
        $subQueryGrade->from('bal_children', array('grade_id'));
        $subQueryGrade->where("child_id = ?", $childId);

        $query = $this->select()->setIntegrityCheck(FALSE);
        $query->from($table, array('child_id', 'grade_id', 'points'));
        $query->joinLeft('bal_children', 'bal_children.child_id = ' . $table . '.child_id', array(
            'coppa_required', 'coppa_accepted', 'avatar', 'bal_children.name', 'bal_children.image', 'bal_children.gender'
        ));

        $query->joinLeft(array('kid_postion' => $subQueryPosition), '1', '');
        $query->joinLeft(array('kid_grade' => $subQueryGrade), '1', '');

        $query->columns(array(
            "find_in_set($table.rank, (select group_concat(distinct $table.rank order by $table.`rank` ASC separator ',') from $table WHERE grade_id = kid_grade.grade_id)) AS rank",
            "find_in_set($table.position, (select group_concat(distinct $table.position order by $table.`position` ASC separator ',') from $table WHERE grade_id = kid_grade.grade_id)) AS position"
        ));

        $query->where("$table.position > IF((kid_postion.position IS NOT NULL AND kid_postion.points <> 0), kid_postion.position - 2, 0) + (" . ($position * $length) . ")");
        $query->where("$table.position < IF((kid_postion.position IS NOT NULL AND kid_postion.points <> 0), kid_postion.position - 1, 1) + (" . ($position * $length + $length) . ")");

        $query->where("$table.grade_id = kid_grade.grade_id"); 
        $query->where("$table.points <> 0");
        $query->where("$table.points IS NOT NULL");
        $query->limit($length);
        return $this->fetchAll($query);
    }

}