<?php

/*
 * This is a model class for frameworks
 * Created By suman khatri
 * 
 */

class Application_Model_DbTable_Framework extends Zend_Db_Table_Abstract
{

    // This is name of Table
    protected $_name = 'bal_subjects';

    public function getAllframeworks($where)
    {
        $data = $this->fetchAll($where)->toArray();
        return $data;
    }

    public function getSubjectLsit()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bls' => 'bal_subjects'), array('bls.subject_id', 'bls.subject_name'))
                ->order('bls.subject_id ASC');
        $sujectInfo = $db->fetchAll($select);
        foreach ($sujectInfo as $key => &$value) {
            if (strtolower($value['subject_name']) == 'math.content') {
                $value['subject_name'] = 'Math';
            }
        }
        return $sujectInfo;
    }

}
