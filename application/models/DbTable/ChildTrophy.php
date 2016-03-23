<?php

/*
 * This is a model class for childtrophy
 * Created By Suman Khatri
 *
 */

class Application_Model_DbTable_ChildTrophy extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_child_trophies';

    //function to check existing trophy according to child id and trophy
    public function getExisttrophy($childId, $title, $description, $image, $subjectId, $gradId) {
        $trophy = $this->fetchRow(array(
            "description = ?" => $description,
            "child_id = ?" => $childId,
            "subject_id = ?" => $subjectId,
            "grade_id = ?" => $gradId,
            "title = ?" => $title,
            "image = ?" => $image
        ));

        return ($trophy != '') ? $trophy->toArray() : FALSE;
    }

    //function to add trophy for child
    public function AddTrophy($data) {
        return $this->insert($data);
    }

    //this function is used to fetch child info according to child id
    public function getChildTrophyData($childId, $searchDate = null, $nextdate = null) {
        if ($searchDate != null && $nextdate != null) {
            $searchDate = date('Y-m-d H:i:s', strtotime($searchDate));
            $nextdate = date('Y-m-d 23:59:59', strtotime($nextdate));
            $where = "bl.awarded_date >= '$searchDate' and bl.awarded_date < '$nextdate'";
        } else {
            $where = 1;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_child_trophies'), array('bl.*'))
                ->joinLeft(array('blg' => 'bal_grades'), 'bl.grade_id = blg.grades_id')
                ->joinLeft(array('bls' => 'bal_subjects'), 'bl.subject_id = bls.subject_id')
                ->where('bl.child_id = ? ', $childId)
                ->where($where)
                ->order('bl.awarded_date DESC');
        $childTrophyInfo = $db->fetchAll($select);
        return $childTrophyInfo;
    }

    //function used to get trophy using trophy id
    public function gettrophyusingId($tId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_child_trophies'), array('bl.*'))
                ->where("bl.child_trophy_id in ($tId)")
                ->group('bl.child_trophy_id');
        $childTrophyInfo = $db->fetchAll($select);
        return $childTrophyInfo;
    }

    /**
     * function for delete child trophy
     * 
     * @param Int $childId
     * @return Int
     */
    public function deleteData($childId) {
        $where = $this->_db->quoteInto("child_id = ?", $childId);
        return $this->delete($where);
    }

    //function to check existing trophy according to child id and trophy
    public function getExisttrophyWithoutGrade($childId, $title, $description, $image, $subjectId) {
        $trophy = $this->fetchRow(array(
            "description = ?" => $description,
            "child_id = ?" => $childId,
            "subject_id = ?" => $subjectId,
            "title = ?" => $title,
            "image = ?" => $image
        ));

        return ($trophy != '');
    }

    /**
     * function for update child trophy
     * 
     * @param type $childtrophyId
     * @param type $dataUpdate
     * @return type
     */
    public function updateData($childtrophyId, $dataUpdate) {
        $where = $this->_db->quoteInto("child_trophy_id = ?", $childtrophyId);
        return $this->update($dataUpdate, $where);
    }

    /**
     * Function to update trophy entries for all childs when a trophy is updated by admin
     */
    public function updateChildTrophyData($updatedData, $trohyId, $trophyType) {

        $where = array("trophy_id = ?" => $trohyId, "type = ?" => $trophyType);
        return $this->update($updatedData, $where);
    }

    /**
     * Get number of trophies per subject
     * and get all subject of the child
     * irrespective of trophy achieved or not
     * 
     * @param int $childId
     * @return Zend_Db_Table_Rowset
     */
    public function getTrophyCountPerSubject($childId, $fromDate, $toDate) {
        $query1 = $this->select()->setIntegrityCheck(FALSE);
        $query1->distinct();
        $query1->from(array('child_trophy' => 'bal_child_trophies'), '');
        $query1->joinLeft(array('child_subjects' => 'bal_child_subjects'), 'child_subjects.subject_id = child_trophy.subject_id AND child_subjects.subject_id = child_trophy.subject_id', '');
        $query1->columns(array('child_subjects.subject_id', 'COUNT(DISTINCT child_trophy_id) as trophy_count'));
        $query1->where('child_trophy.child_id = ?', $childId);
        if ($fromDate != null && $toDate != null) {
            $from = date('Y-m-d H:i:s', strtotime($fromDate));
            $to = date('Y-m-d 23:59:59', strtotime($toDate));
            $query1->where("awarded_date >= '$from' AND awarded_date < '$to'");
        }
        $query1->group('subject_id');

        $query2 = $this->select()->setIntegrityCheck(FALSE);
        $query2->distinct();
        $query2->from(array('child_subjects' => 'bal_child_subjects'), '');
        $query2->joinLeft(array('child_trophy' => 'bal_child_trophies'), 'child_subjects.subject_id = child_trophy.subject_id AND child_subjects.subject_id = child_trophy.subject_id', '');
        $query2->joinLeft(array('streak_sub_trophy' => 'bal_streak_subject_trophies'), 'child_subjects.subject_id = streak_sub_trophy.subject_id', '');
        $query2->joinLeft(array('sub_trophy' => 'bal_subject_trophies'), 'child_subjects.subject_id = sub_trophy.subject_id', '');
        $query2->columns(array('child_subjects.subject_id', new Zend_Db_Expr('0 as trophy_count')));
        $query2->where('child_subjects.child_id = ?', $childId);
        $query2->where('streak_sub_trophy.subject_id != "" OR sub_trophy.subject_id != ""');
        /*if ($fromDate != null && $toDate != null) {
            $from = date('Y-m-d H:i:s', strtotime($fromDate));
            $to = date('Y-m-d 23:59:59', strtotime($toDate));
            $query2->where("awarded_date >= '$from' AND awarded_date < '$to'");
        }*/
        $query2->group('subject_id');
        
        //echo $query2; exit;

        $query3 = $this->select()->setIntegrityCheck(FALSE);
        $query3->distinct();
        $query3->from(array('child_subjects' => 'bal_child_subjects'), '');
        $query3->columns(array(
            new Zend_Db_Expr('0 as subject_id'), new Zend_Db_Expr('0 as trophy_count')
        ));
        
        $queryUnion = $this->select()->setIntegrityCheck(FALSE);
        $queryUnion->union(array($query1, $query2, $query3));

        $query = $this->select()->setIntegrityCheck(FALSE);
        $query->from(array('child_trophy_subject' => $queryUnion), '');
        $query->joinLeft(array('subjects' => 'bal_subjects'), 'subjects.subject_id = child_trophy_subject.subject_id', array('subject_id', 'subject_name'));
        $query->columns(array('SUM(trophy_count) as trophy_count'));
        $query->order(array('ISNULL(subject_name) DESC', 'trophy_count DESC'));
        $query->group('subject_id');
        

        $result = $this->fetchAll($query);
        return $result;
    }

}
