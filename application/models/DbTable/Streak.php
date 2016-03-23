<?php

/*
 * This is a model class for Question Categories
 * Created By Sunil Khanchandani
 *
 */

class Application_Model_DbTable_Streak extends Zend_Db_Table_Abstract {

    // This is name of Table
    protected $_name = 'bal_streak_trophy';

    //function to add categories
    public function addTrophy($data) {
        $options = $this->insert($data);
        return $options;
    }

    //function to update categories Data
    public function updatetrophyData($trophyDataArray, $trophy_id) {
        $where = $this->_db->quoteInto("streak_trophy_id 	=?", $trophy_id);
        $updateCatData = $this->update($trophyDataArray, $where);
        return $updateCatData;
    }

    //this function is used to fetch category info
    public function getTrophiesInfo($searchData = null) {
        if ($searchData != null) {

            $where = "bl.title LIKE " . $this->_db->quote('%' . $searchData . '%') . "";
            $where .= " OR bl.description LIKE " . $this->_db->quote('%' . $searchData . '%') . "";
            $where .= " OR bl.streak LIKE " . $this->_db->quote($searchData) . "";
        } else {
            $where = 1;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bl' => 'bal_streak_trophy'), array('bl.*'))
                ->where($where)
                //->group('grds.grade_name')
                ->order('created_date desc');
        $childInfo = $db->fetchAll($select);
        return $childInfo;
    }

    //function to get domain info
    public function deleteTrophy($trophy_id) {

        $where = $this->_db->quoteInto("streak_trophy_id=?", $trophy_id);
        $deleteCategory = $this->delete($where);
        if ($deleteCategory) {

            return true;
        }
    }

    public function trophyInfo($trophy_id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $this->_db->quoteInto("streak_trophy_id=?", $trophy_id);
        $select = $db->select()
                ->from($this->_name)
                ->where($where);
        $categoryData = $db->fetchRow($select);

        return $categoryData;
    }

    //function to get propertes for points
    public function GetTrophyFortotalPoints($points) {
        $where = "points <= $points";
        $order = "points desc";
        return $this->fetchAll($where, $order)->toArray();
    }

    public function check_title($title) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $this->_db->quoteInto("title=?", $title);
        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($where);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_streak($streak) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $where = $this->_db->quoteInto("streak=?", $streak);
        $select = $db->select()
                ->from($this->_name, array('count(*) as tot'))
                ->where($where);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_title2($title, $id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $where[] = $this->_db->quoteInto("title=?", $title);
        $where[] = $this->_db->quoteInto("streak_trophy_id!=?", $id);
        $cond = implode(' and ', $where);
        $select = $db->select()
                ->from($this->_name, array('count(1) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

    public function check_streak2($streak, $id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $where[] = $this->_db->quoteInto("streak=?", $streak);
        $where[] = $this->_db->quoteInto("streak_trophy_id!=?", $id);
        $cond = implode(' and ', $where);

        $select = $db->select()
                ->from($this->_name, array('count(1) as tot'))
                ->where($cond);
        $categoryData = $db->fetchRow($select);

        return $categoryData['tot'];
    }

}
