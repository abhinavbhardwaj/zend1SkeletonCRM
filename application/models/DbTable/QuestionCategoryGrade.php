<?php
/*
 * This is a model class for Question Categories
 * Created By Sunil Khanchandani
 * 
 */
class Application_Model_DbTable_QuestionCategoryGrade extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_question_category_grades';
	
	/**
	 * function to grades related to particular category 
	 * @param int categoryId
	 * @return Array list
	 */
	public function getgradesInfoUsingCategory($categoryId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();     	 
		$select = $db->select()     	 
			->from(array('blcg' => 'bal_question_category_grades'),     	 		
					array('blcg.*'))     	 		
			->joinLeft(array('blg' => 'bal_grades'),     	 				
					'blcg.grade_id = blg.grades_id',     	 				
					array('blg.*'))     	 	
			->where("blcg.category_id = '$categoryId'")
                        ->order('blg.grades_id');     	
		$gradeInfo =  $db->fetchAll($select);  
		return $gradeInfo;
	}
	
	

	/**
     * function to get subject list
     * @param int gradeId
     * @return Array list
     */
    public function getSubjectListOnGrade($gradeId, $isBibleOnly = false, $noBibleQuestions = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('blcg' => 'bal_question_category_grades'), array(''));
        $select->join(array('blc' => 'bal_question_categories'), 'blcg.category_id = blc.category_id', array(''));
        $select->join(array('blg' => 'bal_grades'), 'blg.grades_id = blcg.grade_id', array(''));
        $select->join(array('bls' => 'bal_subjects'), 'bls.subject_id = blc.subject_id', array('bls.subject_id', 'bls.subject_name'));
        $select->join(array('blst' => 'bal_standards'), 'blst.standard_id = blc.standard_id', array(''));
        $select->join(array('bld' => 'bal_question_domains'), 'blc.domain_id = bld.domain_id', array('bld.*'));
        $select->join(array('question' => 'bal_questions'), 'question.category_id = blc.category_id', array(''));
        $select->where("blg.grades_id='$gradeId'");
        $select->group(array('bld.domain_id', 'bls.subject_name'));

        if ($isBibleOnly) {
            $select->where('((bls.subject_name = "BIBLE New Testament") OR (bls.subject_name = "BIBLE Old Testament"))');
        }

        if ($noBibleQuestions) {
            $select->where('bls.subject_name <> "BIBLE New Testament"');
            $select->where('bls.subject_name <> "BIBLE Old Testament"');
        }

        $select->where('question.is_approved = "Y"');
        $select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');

        $subjectList = $db->fetchAll($select);
        return $subjectList;
    }

    /**
     * function to get subject list of child
     * @param int gradeId,childId
     * @return Array list
     */
    public function getSubjectListOnGradeOfChild($gradeId, $childId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('blcg' => 'bal_question_category_grades'), array('category_id'));
        $select->join(array('blc' => 'bal_question_categories'), 'blcg.category_id = blc.category_id', array('category_id'));
        $select->join(array('blg' => 'bal_grades'), 'blg.grades_id = blcg.grade_id', array('grade_name'));
        $select->join(array('bls' => 'bal_subjects'), 'bls.subject_id = blc.subject_id', array('bls.*'));
        $select->join(array('blst' => 'bal_standards'), 'blst.standard_id = blc.standard_id', array('blst.standard_id'));
        $select->joinLeft(array('bcs' => 'bal_child_subjects'), 'bls.subject_id = bcs.subject_id', array('bcs.child_id'));
        //$select->join(array('question' => 'bal_questions'), 'question.category_id = blc.category_id', array(''));

        $select->where("bcs.child_id = '$childId'");
        $select->where("blg.grades_id='$gradeId'");

        $select->group(array('bls.subject_id'));

        //$select->where('question.is_approved = "Y"');
        //$select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');

        $subjectList = $db->fetchAll($select);

        foreach ($subjectList as &$subject) {
            if ($subject['subject_name'] == 'math.content') {
                $subject['subject_name'] = 'Math';
            }
        }

        return $subjectList;
    }

    /**
     * function to get subject list of child
     * @param int gradeId,childId
     * @return Array list
     */
    public function getAllSubjectListOnGrade($gradeId, $subId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('blcg' => 'bal_question_category_grades'), array('category_id'));
        $select->joinLeft(array('blc' => 'bal_question_categories'), 'blcg.category_id = blc.category_id', array('category_id'));
        $select->joinLeft(array('blg' => 'bal_grades'), 'blg.grades_id = blcg.grade_id', array('grade_name'));
        $select->joinLeft(array('bls' => 'bal_subjects'), 'bls.subject_id = blc.subject_id', array('bls.*'));
        //$select->joinLeft(array('bcs' => 'bal_child_subjects'), 'bls.subject_id != bcs.subject_id', array('bcs.child_id'));
                
        $select->where("blg.grades_id IN ($gradeId)");
        $select->where("bls.subject_id NOT IN ($subId)");
        $select->group('bls.subject_id');
        
        $select->join(array('question' => 'bal_questions'), 'question.category_id = blc.category_id', array(''));
        $select->where('question.is_approved = "Y"');
        $select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');

        $subjectList = $db->fetchAll($select);
        return $subjectList;
    }

    /**
	 * function to get category list of asked question of child
	 * @param int gradeId,childId,subjectId
	 * @return Array list
	 */
	public function getCategoryListOnGradeOfChild($gradeId,$childId,$subjectId)
	{		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blcg' => 'bal_question_category_grades'),
		array('category_id as cateId'))
		->joinLeft(array('blc' => 'bal_question_categories'),
					'blcg.category_id = blc.category_id',     	 				
		array('category_id'))
		->joinLeft(array('blg' => 'bal_grades'),
					'blg.grades_id = blcg.grade_id',     	 				
		array('grade_name'))
		->joinLeft(array('bls' => 'bal_subjects'),
					'bls.subject_id = blc.subject_id',     	 				
		array('bls.*'))
		->joinLeft(array('blst' => 'bal_standards'),
					'blst.standard_id = blc.standard_id',     	 				
		array('blst.standard_id'))
		->joinLeft(array('bcs' => 'bal_child_subjects'),
					'bls.subject_id = bcs.subject_id',     	 				
		array('bcs.child_id'))
		->where("blg.grades_id =$gradeId")
		//->where("bcs.child_id = '$childId'")
		->where("blc.subject_id = $subjectId")
		->group("cateId");
		$categoryList =  $db->fetchAll($select);
		return $categoryList;
	}
}