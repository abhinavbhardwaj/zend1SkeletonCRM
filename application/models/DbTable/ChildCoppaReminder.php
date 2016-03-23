<?php

/**
 * 
 */
class Application_Model_DbTable_ChildCoppaReminder extends Zend_Db_Table_Abstract
{

    // This is the name of Table
    protected $_name = 'bal_children_coppa_reminder';

    public function getChildListToRemind()
    {
        $query = $this->select()->setIntegrityCheck(FALSE);

        $query->from(array('bc' => 'bal_children'), array('bc.child_id', 'bc.name as child_name', 'bc.dob as child_dob', 'bc.coppa_accepted'));
        $query->join(array('bp' => 'bal_parents'), 'bc.parent_id = bp.parent_id', array('bp.parent_id'));
        $query->joinLeft(array('bcr' => 'bal_children_coppa_reminder'), 'bc.child_id = bcr.child_id', array('last_reminder', 'reminder_count'));

        $query->where('bc.coppa_required = 1');
    //    $query->where('bc.coppa_accepted = 0');
        $query->where('bp.parent_id <> 0');
        return $this->fetchAll($query);
    }

    public function resetReminder($childId)
    {
        $this->delete(array('child_id = ?' => $childId));
        $this->insert(array('child_id' => $childId));
    }

    public function incrementReminderCount($childId)
    {
        $data = array('last_reminder' => new Zend_Db_Expr('NOW()'), 'reminder_count' => new Zend_Db_Expr('reminder_count + 1'));
        $isUpdated = $this->update($data, array('child_id = ?' => $childId));

        if (!$isUpdated) {
            $this->insert(array('child_id' => $childId, 'reminder_count' => 1));
        }
    }

    public function createReminderToken($childId)
    {
        $token = My_Functions::generateRandomString();
        $isUpdated = $this->update(array('token' => $token), array('child_id = ?' => $childId));

        if (!$isUpdated) {
            $this->insert(array('child_id' => $childId, 'token' => $token));
        }
        return $token;
    }

    public function isValidReminderToken($childId, $token)
    {
        if (empty($token)) {
            return FALSE;
        }
        $data = $this->fetchRow(array('child_id = ?' => $childId, 'token = ?' => $token));
        if (!empty($data)) {
            return TRUE;
        }

        return FALSE;
    }

}
