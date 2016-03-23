<?php

/*
 * This is a model class for Question Domain 
 * Created By Sunil Khanchandani
 * 
 */

class Application_Model_DbTable_QuestionGrade extends Zend_Db_Table_Abstract {

    // This is the name of Table
    protected $_name = 'bal_question_category_grades';

    public function addGradeData($gradeData) {

        $gradeDataInsert = $this->insert($gradeData);
        return $gradeDataInsert;
    }

}
